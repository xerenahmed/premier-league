<script setup>
import {onMounted, reactive} from 'vue'
import Fixture from "@/components/Fixture.vue";

const props = defineProps(['open']);
defineEmits(['onClose']);

const state = reactive({ data: null, loading: true, error: null })

const fetchData = () => {
    axios.get('/api/fixtures/all-weeks')
        .then((response) => {
            state.data = response.data.data;
        })
        .catch((err) => {
            state.error = err.message;
        })
        .finally(() => {
            state.loading = false;
        })
}
onMounted( () => {
    fetchData();
});
</script>

<template>
      <div v-if="open" class="modal">
          <div class="inner">
              <button class="close-btn" @click="$emit('onClose')">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                  </svg>
                  <span>Close</span>
              </button>

              <h1 class="heading">Fixtures</h1>

              <div class="mt-6">
                  <div v-if="state.loading">
                      Loading...
                  </div>
                  <div v-else-if="!!state.error">
                      Error: {{ state.error }}
                  </div>
                  <div v-else class="grid grid-rows-3 grid-cols-2 gap-x-16 gap-y-4">
                      <div v-for="(entry, index) in state.data" :key="index">
                          <h2 class="mb-4 text-xl">Week {{ entry.round + 1 }}</h2>
                          <hr class="mb-4"/>

                          <div class="grid grid-cols-1 gap-4">
                              <Fixture v-for="fixture in entry.fixtures" v-bind="fixture" :freeze="false" />
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
</template>

<style scoped>
.modal {
    @apply bg-white min-h-60 container mx-auto rounded-lg border shadow-xl;
    position: fixed;
    z-index: 999;
    top: 6%;
    left: 6%;
    transition: all 500ms;
}
.inner {
    @apply p-8 relative;
}
.heading {
    font-family: Premier-Sans-Heavy;
    @apply text-3xl;
}
.close-btn {
    @apply flex rounded border px-4 py-2 border-gray-500 gap-2 items-center absolute top-4 right-4;
}
.close-btn span {
    @apply text-lg;
}
.close-btn:hover {
    @apply bg-slate-300;
}
</style>
