<script setup>
import Fixture from "./Fixture.vue";
import {ref} from "vue";

const entries = await axios.get('/api/fixtures/weekly').then(response => response.data.data);

const emit = defineEmits(['simulationStarted'])
const started = ref(false);

const onClick = () => {
    started.value = true;
    axios.post('/api/league/start').then(() => {
        emit('simulationStarted');
    }).catch(() => {
        started.value = false;
    })
}

</script>

<template>
    <div class="root lg:pt-20">

        <div class="container mx-auto bg-white p-12 rounded-lg">
            <h1 class="mb-6 text-3xl">Fixtures</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div v-for="entry in entries" :key="entry.id" class="entry">
                    <h2 class="mb-2">Week {{ entry.round + 1 }}</h2>
                    <hr class="mb-2"/>
                    <Fixture
                        v-for="fixture in entry.fixtures"
                        :key="fixture.id"
                        v-bind="fixture"
                        class="mb-4"
                        :freeze="false"
                        :teamProps="{
                        imgSize: 50,
                        textProps: {
                            class: 'text-lg'
                        }
                    }"
                    />
                </div>
            </div>
            <div class="mt-6">
                <button style="background: var(--theme-color); color: white;" class="px-3 rounded-lg py-3 text-lg flex gap-2 items-center" @click="onClick" :disabled="started">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                        <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                    </svg>

                    Start Simulation
                </button>
            </div>
        </div>

    </div>
</template>

<style scoped>
h2 {
    font-family: Premier-Sans-Heavy;
    @apply text-2xl;
}

.entry {
    @apply border px-5 pt-4 pb-2 rounded-lg;
    animation: scaleup 500ms;
}

@keyframes scaleup {
    from {
        scale: 0.4;
    }
    to {
        scale: 1;
    }
}
</style>
