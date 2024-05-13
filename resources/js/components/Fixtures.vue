<script setup>
import Fixture from "./Fixture.vue";
import {onMounted,reactive} from "vue";

defineEmits(['goalChanged']);
const props = defineProps(['endpoint'])
const state = reactive({
    data: null,
    error: null,
    loading: true
});

const fetchData = () => {
    fetch(props.endpoint ?? '/api/fixtures')
        .then((resp) => resp.json())
        .then((value) => {
            state.data = value;
            state.error = null;
        })
        .catch((err) => {
            state.error = err;
        })
        .finally(() => {
            state.loading = false;
        });
}
onMounted(fetchData);
defineExpose({mutate: fetchData});
</script>

<template>
    <div v-if="state.loading">
        Loading
    </div>
    <div v-else-if="state.error">
        Error: {{ state.error }}
    </div>
    <div v-else class="flex flex-col gap-3">
        <slot :league="state.data.data.league">
            <h2 class="text-xl">Week {{ state.data.data.league.round + 1 }}</h2>
            <hr class="mb-1"/>
        </slot>

        <Fixture
            v-for="fixture in state.data.data.fixtures"
            :key="fixture.id"
            :freeze="false"
            v-bind="fixture"
            @goal-changed="$emit('goalChanged')"
        />
    </div>
</template>

<style scoped>
.view-all-button {
    background: var(--theme-color);
    color: white;
    width: fit-content;
    @apply px-2 py-1;
}
h2 {
    font-family: Premier-Sans-Heavy;
    @apply text-3xl font-semibold;
}
</style>
