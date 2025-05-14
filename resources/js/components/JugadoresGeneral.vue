<template>
    <div class="jugadores-container">
        <h1>Mejores Jugadores</h1>
        <div class="jugadores-grid">
            <div v-for="jugador in mejoresJugadores" :key="jugador.id_jugador" class="jugador-card">
                <a :href="`jugadores/${jugador.id_jugador}`">
                    <img :src="`assets/usuarios/${jugador.usuario?.imagen_usuario}`"
                         alt="Imagen del Jugador"
                         class="jugador-image"/>
                    <h2>{{ jugador.usuario?.username }}</h2>
                    <div class="jugador-info">
                        <p class="jugador-rango">Rango: {{ jugador.rango_valorant }}</p>
                        <p class="jugador-tag">Rol: {{ roles[jugador.id_rol] }}</p>
                        <p class="jugador-kda">KDA: {{ calcularKDA(jugador) }}</p>
                        <p class="jugador-score">Score: {{ jugador.playerScore.toFixed(2) }}</p>
                    </div>
                </a>
            </div>
        </div>

        <h1>Jugadores</h1>

        <div class="filtros-container" v-if="usuario && usuario.tipo_usuario === 'entrenador'">
            <button
                class="filter-btn"
                :class="{ active: mostrarSoloSinEquipo }"
                @click="toggleFiltroSinEquipo">
                <i class="fas fa-filter"></i>
                {{ mostrarSoloSinEquipo ? 'Mostrar Todos los Jugadores' : 'Mostrar Solo Jugadores Sin Equipo' }}
            </button>
        </div>

        <div class="table-responsive">
            <table class="info-table jugadores-table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Nickname</th>
                        <th>Rango</th>
                        <th>Rol</th>
                        <th>K</th>
                        <th>D</th>
                        <th>A</th>
                        <th>KDA</th>
                        <th>Palmarés</th>
                        <th v-if="usuario && usuario.tipo_usuario === 'entrenador'" >Contratar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="jugador in jugadoresFiltrados" :key="jugador.id_jugador">
                        <td>
                            <img :src="`assets/usuarios/${jugador.usuario.imagen_usuario}`"
                                 alt="Imagen del Jugador"
                                 class="jugador-imagen-tabla" />
                        </td>
                        <td>{{ jugador.usuario ? jugador.usuario.nombre + ' ' + jugador.usuario.apellidos : 'Sin nombre' }}</td>
                        <td class="nickname">{{ jugador.usuario ? jugador.usuario.nombre_jugador || jugador.usuario.username : 'Sin nickname' }}</td>
                        <td>{{ jugador.rango_valorant }}</td>
                        <td>{{ roles[jugador.id_rol] }}</td>
                        <td class="kills">{{ jugador.kills }}</td>
                        <td class="deaths">{{ jugador.deaths }}</td>
                        <td class="assists">{{ jugador.assists }}</td>
                        <td class="kda">{{ calcularKDA(jugador) }}</td>
                        <td>{{ jugador.palmares }}</td>
                        <td v-if="usuario && usuario.tipo_usuario === 'entrenador'">
                            <button
                                v-if="!jugador.id_entrenador"
                                class="contratar-btn"
                                @click="mostrarConfirmacion(jugador)">
                                Agregar al Equipo
                            </button>
                            <button
                                v-else
                                class="tiene-equipo-btn"
                                disabled>
                                Ya tiene Equipo
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="showModal" class="modal-overlay">
            <div class="modal-content">
                <h3>Confirmar Contratación</h3>
                <p>¿Estás seguro de que deseas agregar a {{ jugadorSeleccionado?.usuario?.nombre_jugador || 'este jugador' }} a tu equipo?</p>
                <div class="modal-buttons">
                    <button @click="confirmarContratacion" class="btn-confirm">Confirmar</button>
                    <button @click="showModal = false" class="btn-cancel">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
export default {
    name: 'JugadoresGeneral',
    props: {
        usuario: {
            type: Object,
            required: false,
            default: null
        }
    },
    data() {
        return {
            jugadores: [],
            mostrarSoloSinEquipo: false,
            showModal: false,
            jugadorSeleccionado: null,
            roles: {
                1: "Duelista",
                2: "Iniciador",
                3: "Centinela",
                4: "Controlador"
            },
            rangosValoracion: {
                'Hierro': 1,
                'Bronce': 2,
                'Plata': 3,
                'Oro': 4,
                'Platino': 5,
                'Diamante': 6,
                'Ascendente': 7,
                'Inmortal': 8,
                'Radiante': 9
            }
        }
    },
    computed: {
        isEntrenador() {
            return this.usuario && this.usuario.tipo_usuario === 'entrenador';
        },
        jugadoresFiltrados() {
        if (this.mostrarSoloSinEquipo) {
            return this.jugadores.filter(jugador => !jugador.id_entrenador);
        }
        return this.jugadores;
    },
        mejoresJugadores() {
        return this.jugadores
            .map(jugador => ({
                ...jugador,
                playerScore: this.calcularPlayerScore(jugador)
            }))
            .sort((a, b) => b.playerScore - a.playerScore)
            .slice(0, 6);  // Esto limita a 6 jugadores
        }
    },
    methods:{
        toggleFiltroSinEquipo() {
            this.mostrarSoloSinEquipo = !this.mostrarSoloSinEquipo;
        },
        mostrarConfirmacion(jugador) {
            this.jugadorSeleccionado = jugador;
            this.showModal = true;
        },
        confirmarContratacion() {
        if (!this.jugadorSeleccionado || !this.usuario) {
            this.mostrarMensaje('Error: No se puede realizar la operación', 'error');
            return;
        }

        axios.post('contratar-jugador/', {
            jugador_id: this.jugadorSeleccionado.id_jugador,
            entrenador_id: this.usuario.id_usuario
        })
        .then(response => {
            // Actualizar la lista de jugadores
            this.fetchJugadores();
            this.showModal = false;
            this.jugadorSeleccionado = null;
            this.mostrarMensaje('Jugador contratado exitosamente', 'success');
        })
        .catch(error => {
            console.error('Error al contratar jugador:', error);
            this.mostrarMensaje('Error al contratar jugador: ' + error.response?.data?.message || 'Error desconocido', 'error');
        });
    },
        mostrarMensaje(mensaje, tipo) {
            // Implementa aquí tu lógica de notificación
            alert(mensaje);
        },
        calcularKDA(jugador) {
            if (!jugador.deaths || jugador.deaths === 0) {
                return 'Perfect';
            }
            return ((jugador.kills + jugador.assists) / jugador.deaths).toFixed(2);
        },
        calcularPlayerScore(jugador) {
            const kda = jugador.deaths === 0 ?
                (jugador.kills + jugador.assists) :
                (jugador.kills + jugador.assists) / jugador.deaths;

            const rangoValue = this.rangosValoracion[jugador.rango_valorant] || 1;


            const rolFactor = {
                1: 1.2, // Duelista
                2: 1.1, // Iniciador
                3: 1.0, // Centinela
                4: 1.1  // Controlador
            }[jugador.id_rol] || 1;

            return (kda * 0.4 + rangoValue * 0.4) * rolFactor;
        },
        fetchJugadores() {
            axios.get('api/mercadojugadores')
                .then(response => {
                    console.log('Datos recibidos:', response.data);
                    this.jugadores = response.data;
                })
                .catch(error => {
                    console.error('Error al obtener jugadores:', error);
                });
        }
    },
    mounted() {
        this.fetchJugadores();
    }
};
</script>

<style scoped>

.tiene-equipo-btn {
    background-color: #fc1414;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: not-allowed;
    opacity: 0.7;
    font-size: 0.9em;
}

.tiene-equipo-btn:hover {
    background-color: #666;
}

.filtros-container {
    margin-bottom: 20px;
    text-align: left;
}

.filter-btn {
    background-color: #333;
    color: white;
    border: 1px solid #FE4454;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-btn.active {
    background-color: #FE4454;
}

.contratar-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.contratar-btn:hover {
    background-color: #45a049;
}

/* Modal styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: #2a2a2a;
    padding: 20px;
    border-radius: 8px;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.modal-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.btn-confirm {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-cancel {
    background-color: #f44336;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.jugadores-container {
    padding: 30px;
    text-align: center;
    background-color: #1E1E1E;
    color: #ffffff;
    border-radius: 12px;
    margin: 20px;
}

h1 {
    color: #ffffff;
    font-size: 2.2em;
    margin-bottom: 30px;
    border-bottom: 1px solid #FE4454;
    padding-bottom: 15px;
    text-align: center;
}

.jugadores-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.jugador-card {
    border: 1px solid #FE4454;
    border-radius: 8px;
    padding: 20px 15px;
    background-color: #2a2a2a;
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.jugador-card:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(254, 68, 84, 0.3);
}

.jugador-image {
    width: 120px;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
}

.jugador-card h2 {
    color: #FE4454;
    font-size: 1.4em;
    margin: 10px 0;
    text-align: center;
    width: 100%;
}

.jugador-tag {
    color: #ccc;
    font-size: 0.85em;
}

.jugador-info {
    margin-top: 10px;
    padding: 10px 0;
    width: 100%;
    border-top: 1px solid #444;
    border-bottom: 1px solid #444;
}

.jugador-rango {
    font-style: italic;
    color: #ccc;
    margin: 5px 0;
    font-size: 0.95em;
}

a {
    text-decoration: none;
    color: inherit;
    display: block;
    width: 100%;
    height: 100%;
}

.ver-mas {
    margin-top: 15px;
    color: #FE4454;
    font-size: 0.9em;
    text-decoration: underline;
    opacity: 0;
    transition: opacity 0.2s;
}

.jugador-card:hover .ver-mas {
    opacity: 1;
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 15px;
}

.info-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #2a2a2a;
}

.info-table th,
.info-table td {
    border: 1px solid #444;
    padding: 12px;
    text-align: center;
}

.info-table th {
    background-color: #333;
    color: #FE4454;
    font-weight: bold;
}

.info-table tr:nth-child(even) {
    background-color: #222;
}

.info-table tr:hover {
    background-color: #383838;
}

.jugador-imagen-tabla {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.kills {
    color: #4CAF50;
    font-weight: bold;
}

.deaths {
    color: #F44336;
    font-weight: bold;
}

.assists {
    color: #2196F3;
    font-weight: bold;
}

.kda {
    color: #FFC107;
    font-weight: bold;
}

@media (max-width: 768px) {
    .info-table th,
    .info-table td {
        padding: 8px;
        font-size: 0.9em;
    }
}

@media (max-width: 768px) {
    .jugadores-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }

    .jugador-image {
        width: 100px;
        height: 120px;
    }
}
</style>
