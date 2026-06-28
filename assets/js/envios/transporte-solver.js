/**
 * transporte-solver.js
 *
 * Solucionador de problemas de transporte (Esquina Noroeste + Método MODI).
 * Reutilizado del archivo TransportationSolverJSON.php original del usuario,
 * extraído tal cual a un módulo independiente para usarse en la vista de
 * "Programación de Envíos" del CIIT TMS.
 */

class TransporteSolverJS {
    constructor() {
        this.epsilon = 1e-8;
    }

    resolver(costos, oferta, demanda, nombresOrigenes, nombresDestinos) {
        let numOrigenes = oferta.length;
        let numDestinos = demanda.length;
        let matrizCostos = JSON.parse(JSON.stringify(costos));
        let vectorOferta = [...oferta];
        let vectorDemanda = [...demanda];
        let origenesNombres = [...nombresOrigenes];
        let destinosNombres = [...nombresDestinos];

        // Balancear el problema
        let totalOferta = vectorOferta.reduce((a, b) => a + b, 0);
        let totalDemanda = vectorDemanda.reduce((a, b) => a + b, 0);
        let balanceado = Math.abs(totalOferta - totalDemanda) < this.epsilon;

        if (!balanceado) {
            if (totalOferta > totalDemanda) {
                for (let i = 0; i < numOrigenes; i++) {
                    matrizCostos[i].push(0);
                }
                vectorDemanda.push(totalOferta - totalDemanda);
                destinosNombres.push("DUMMY (excedente)");
                numDestinos++;
            } else {
                matrizCostos.push(Array(numDestinos).fill(0));
                vectorOferta.push(totalDemanda - totalOferta);
                origenesNombres.push("DUMMY (faltante)");
                numOrigenes++;
            }
        }

        // Esquina Noroeste
        let solucion = Array(numOrigenes).fill().map(() => Array(numDestinos).fill(0));
        let ofertaRestante = [...vectorOferta];
        let demandaRestante = [...vectorDemanda];
        let i = 0, j = 0;

        while (i < numOrigenes && j < numDestinos) {
            let asignacion = Math.min(ofertaRestante[i], demandaRestante[j]);
            if (asignacion > 0) {
                solucion[i][j] = asignacion;
            }
            ofertaRestante[i] -= asignacion;
            demandaRestante[j] -= asignacion;
            if (ofertaRestante[i] < this.epsilon) i++;
            if (demandaRestante[j] < this.epsilon) j++;
        }

        let costoActual = 0;
        for (let i = 0; i < numOrigenes; i++) {
            for (let j = 0; j < numDestinos; j++) {
                costoActual += solucion[i][j] * matrizCostos[i][j];
            }
        }

        // Método MODI
        let iteracion = 0;
        const maxIteraciones = 100;

        while (iteracion < maxIteraciones) {
            iteracion++;

            // Obtener celdas base
            let celdasBase = [];
            for (let i = 0; i < numOrigenes; i++) {
                for (let j = 0; j < numDestinos; j++) {
                    if (solucion[i][j] > this.epsilon) {
                        celdasBase.push([i, j]);
                    }
                }
            }

            // Manejar degeneración
            let celdasNecesarias = numOrigenes + numDestinos - 1;
            if (celdasBase.length < celdasNecesarias) {
                for (let i = 0; i < numOrigenes && celdasBase.length < celdasNecesarias; i++) {
                    for (let j = 0; j < numDestinos && celdasBase.length < celdasNecesarias; j++) {
                        if (solucion[i][j] < this.epsilon && !this.estaEnCiclo(celdasBase, i, j)) {
                            solucion[i][j] = 0;
                            celdasBase.push([i, j]);
                        }
                    }
                }
            }

            // Calcular potenciales
            let u = Array(numOrigenes).fill(null);
            let v = Array(numDestinos).fill(null);
            u[0] = 0;

            let cambiado = true;
            let iterPot = 0;
            while (cambiado && iterPot < 100) {
                cambiado = false;
                iterPot++;
                for (let celda of celdasBase) {
                    let [i, j] = celda;
                    if (u[i] !== null && v[j] === null) {
                        v[j] = matrizCostos[i][j] - u[i];
                        cambiado = true;
                    } else if (u[i] === null && v[j] !== null) {
                        u[i] = matrizCostos[i][j] - v[j];
                        cambiado = true;
                    }
                }
            }

            for (let i = 0; i < numOrigenes; i++) if (u[i] === null) u[i] = 0;
            for (let j = 0; j < numDestinos; j++) if (v[j] === null) v[j] = 0;

            // Buscar celda entrante
            let mejorI = -1, mejorJ = -1;
            let mejorMejora = 0;

            for (let i = 0; i < numOrigenes; i++) {
                for (let j = 0; j < numDestinos; j++) {
                    if (solucion[i][j] < this.epsilon) {
                        let costoReducido = matrizCostos[i][j] - (u[i] + v[j]);
                        if (costoReducido < -this.epsilon && costoReducido < mejorMejora) {
                            mejorMejora = costoReducido;
                            mejorI = i;
                            mejorJ = j;
                        }
                    }
                }
            }

            if (mejorI === -1) break;

            // Encontrar ciclo
            let ciclo = this.encontrarCiclo(solucion, mejorI, mejorJ, numOrigenes, numDestinos);
            if (!ciclo || ciclo.length < 4) break;

            // Encontrar mínima cantidad
            let minValor = Infinity;
            for (let k = 1; k < ciclo.length; k += 2) {
                let valor = solucion[ciclo[k][0]][ciclo[k][1]];
                if (valor < minValor - this.epsilon) {
                    minValor = valor;
                }
            }

            if (minValor === Infinity || minValor <= this.epsilon) break;

            // Reasignar
            for (let k = 0; k < ciclo.length; k++) {
                let [f, c] = ciclo[k];
                if (k % 2 === 0) {
                    solucion[f][c] += minValor;
                } else {
                    solucion[f][c] -= minValor;
                }
            }

            for (let i = 0; i < numOrigenes; i++) {
                for (let j = 0; j < numDestinos; j++) {
                    if (Math.abs(solucion[i][j]) < this.epsilon) solucion[i][j] = 0;
                }
            }

            costoActual = 0;
            for (let i = 0; i < numOrigenes; i++) {
                for (let j = 0; j < numDestinos; j++) {
                    costoActual += solucion[i][j] * matrizCostos[i][j];
                }
            }
        }

        // Preparar resultados - SOLO para orígenes y destinos originales (sin dummy)
        let envios = [];
        for (let i = 0; i < oferta.length; i++) {
            for (let j = 0; j < demanda.length; j++) {
                if (solucion[i][j] > 0) {
                    envios.push({
                        origen: nombresOrigenes[i],
                        destino: nombresDestinos[j],
                        cantidad: solucion[i][j],
                        costo_unitario: costos[i][j],
                        costo_total: solucion[i][j] * costos[i][j]
                    });
                }
            }
        }

        // Matriz de solución completa (incluyendo dummys si existen)
        let matrizSolucionCompleta = [];
        for (let i = 0; i < numOrigenes; i++) {
            matrizSolucionCompleta[i] = [];
            for (let j = 0; j < numDestinos; j++) {
                matrizSolucionCompleta[i][j] = solucion[i][j];
            }
        }

        return {
            success: true,
            costo_total: costoActual,
            envios: envios,
            solucion_matriz: matrizSolucionCompleta,
            total_oferta: totalOferta,
            total_demanda: totalDemanda,
            balanceado: balanceado,
            datos_entrada: {
                costos: costos,
                oferta: oferta,
                demanda: demanda,
                nombresOrigenes: nombresOrigenes,
                nombresDestinos: nombresDestinos
            },
            estadisticas: {
                numero_envios: envios.length,
                numero_origenes: oferta.length,
                numero_destinos: demanda.length,
                iteraciones_modi: iteracion
            }
        };
    }

    encontrarCiclo(solucion, filaEntrada, colEntrada, numOrigenes, numDestinos) {
        let matriz = Array(numOrigenes).fill().map(() => Array(numDestinos).fill(0));
        for (let i = 0; i < numOrigenes; i++) {
            for (let j = 0; j < numDestinos; j++) {
                matriz[i][j] = (solucion[i][j] > 0) ? 1 : 0;
            }
        }
        matriz[filaEntrada][colEntrada] = 2;
        let ciclo = [];
        this.buscarCiclo(matriz, filaEntrada, colEntrada, filaEntrada, colEntrada, ciclo, numOrigenes, numDestinos, true);
        return ciclo.length > 0 ? ciclo : null;
    }

    buscarCiclo(matriz, filaActual, colActual, filaInicio, colInicio, ciclo, numOrigenes, numDestinos, horizontal) {
        ciclo.push([filaActual, colActual]);

        if (horizontal) {
            for (let j = 0; j < numDestinos; j++) {
                if (j === colActual) continue;
                let esCeldaValida = (matriz[filaActual][j] >= 1);
                let esCeldaInicio = (filaActual === filaInicio && j === colInicio);

                if (esCeldaValida || esCeldaInicio) {
                    if (esCeldaInicio && ciclo.length >= 4) return true;
                    if (!this.repetidoCiclo(ciclo, filaActual, j)) {
                        if (this.buscarCiclo(matriz, filaActual, j, filaInicio, colInicio, ciclo, numOrigenes, numDestinos, false)) {
                            return true;
                        }
                    }
                }
            }
        } else {
            for (let i = 0; i < numOrigenes; i++) {
                if (i === filaActual) continue;
                let esCeldaValida = (matriz[i][colActual] >= 1);
                let esCeldaInicio = (i === filaInicio && colActual === colInicio);

                if (esCeldaValida || esCeldaInicio) {
                    if (esCeldaInicio && ciclo.length >= 4) return true;
                    if (!this.repetidoCiclo(ciclo, i, colActual)) {
                        if (this.buscarCiclo(matriz, i, colActual, filaInicio, colInicio, ciclo, numOrigenes, numDestinos, true)) {
                            return true;
                        }
                    }
                }
            }
        }

        ciclo.pop();
        return false;
    }

    repetidoCiclo(ciclo, fila, col) {
        return ciclo.some(p => p[0] === fila && p[1] === col);
    }

    estaEnCiclo(celdasBase, fila, col) {
        return celdasBase.some(c => c[0] === fila && c[1] === col);
    }
}
