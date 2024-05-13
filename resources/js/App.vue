<script setup>
import {onMounted, reactive} from "vue";
import Simulation from "./components/Simulation.vue";
import SimulationOverview from "./components/SimulationOverview.vue";
import TeamsIntro from "./components/TeamsIntro.vue";
import Logo from "./components/Logo.vue";

const league = reactive({data: null, loading: true});

const fetchData = () => {
    league.loading = true;
    axios.get('/api/league')
        .then((response) => {
            league.data = response.data.data;
        })
        .finally(() => league.loading = false);
}

onMounted(() => {
    setTimeout(fetchData, 600);
});

let logoStyles = {
    position: 'fixed',
    left: '43%',
    top: '40%',
    'z-index': 0
}

</script>
<template>
    <Suspense>
        <div v-if="league.loading">
            <Logo :style="logoStyles"/>
        </div>

        <div v-else-if="league.data.started">
            <Simulation @on-reset="fetchData" :league="league.data"/>
        </div>

        <div v-else-if="league.data.hasFixtures">
            <SimulationOverview @simulation-started="fetchData"/>
        </div>

        <div v-else>
            <TeamsIntro @fixtures-generated="fetchData"/>
        </div>
        <template #fallback>
            <Logo :style="logoStyles"/>
        </template>
    </Suspense>
</template>

