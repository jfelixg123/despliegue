<template>
    <div class="torneos-container">
        <h1>Lista de Torneos</h1>
        <div class="torneos-grid">
            <div v-for="torneo in torneos" :key="torneo.id_torneos" class="torneo-card">
                <a :href="`torneos/individual/${torneo.id_torneos}`">
                <img :src="`assets/torneos/${torneo.imagen_torneo}`" alt="Imagen del Torneo" class="torneo-image" />
                <h2>{{ torneo.nombre_torneo }}</h2>
                <p class="torneo-ubicacion">{{ torneo.ubicacion }}</p>
                <p>Fecha: {{ torneo.fecha_inico }} - {{ torneo.fecha_fin }}</p>
                <div class="ver-mas">Ver detalles</div>
                </a>
            </div>
        </div>
    </div>
</template>


<script>
import axios from 'axios';

export default {
    name: 'Torneos', // array que guarda los torneos
    data() {
        return {
            torneos: [],
        };
    },
    methods: {
        fetchTorneos() {
            axios.get('torneos')
                .then(response => {
                    this.torneos = response.data;
                })
                .catch(error => {
                    console.error('Error al obtener los torneos:', error);
                });
        },
    },
    mounted() {
        this.fetchTorneos(); // Llama a la API al montar el componente
    },
};
</script>

<style scoped>
.torneos-container {
    padding: 30px;
    text-align: center;
    background-color: #1E1E1E;
    color: #ffffff;
    border-radius: 12px;
    margin: 20px;
}

.torneos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.torneo-card {
    border: 1px solid #FE4454;
    border-radius: 8px;
    padding: 15px;
    transition: transform 0.2s, box-shadow 0.2s;
    background-color: #2a2a2a;
}

.torneo-card:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(254, 68, 84, 0.4);
}

.torneo-image {
    width: 120px;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}

h1 {
    color: #ffffff;
    font-size: 2em;
    margin-bottom: 30px;
    border-bottom: 1px solid #FE4454;
    padding-bottom: 10px;
}

h2 {
    margin: 10px 0;
    font-size: 1.5em;
    color: #FE4454;
}

p {
    margin: 5px 0;
    color: #ccc;
}

.torneo-ubicacion {
    font-style: italic;
    color: #aaa;
}

a {
    text-decoration: none;
    color: inherit;
    display: block;
}

.ver-mas {
    margin-top: 15px;
    color: #FE4454;
    font-size: 0.9em;
    text-decoration: underline;
    opacity: 0;
    transition: opacity 0.2s;
}

.torneo-card:hover .ver-mas {
    opacity: 1;
}
</style>
