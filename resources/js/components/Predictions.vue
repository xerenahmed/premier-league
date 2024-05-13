<script setup>
import {onMounted, reactive, ref} from "vue";

const state = reactive({
    data: null,
    error: null,
    initialLoading: true,
});

const fetchData = async () => {
    return axios.get('/api/league/predictions', {
        validateStatus: () => true
    })
        .then((response) => {
            state.data = response.data;
            state.error = null;
        })
        .catch((err) => {
            state.error = err;
        })
        .finally(() => {
            state.initialLoading = false;
            state.loading = false;
        });
}
onMounted(fetchData);
defineExpose({mutate: fetchData});
</script>

<template>
    <div v-if="state.initialLoading">
        Loading
    </div>
    <div v-else-if="state.error">
        Error: {{ state.error }}
    </div>
    <div v-else class="flex flex-col gap-3">
        <slot>
            <h2 class="text-xl">Predictions</h2>
            <hr class="mb-1"/>
        </slot>

        <div v-if="typeof state.data.data == 'string'">
            <p>Predictions will be available after round 3</p>
        </div>
        <div v-else v-for="entry in state.data.data">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img :src="entry.team.flag_url" :height="50" :width="50" />
                    <span class="text-lg">{{ entry.team.name }}</span>
                </div>
                <span class="text-xl">% {{ entry.chance }}</span>
            </div>
        </div>
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
