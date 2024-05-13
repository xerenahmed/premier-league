<script setup>
import LeagueTable from "./LeagueTable.vue";
import PlayAllWeeksButton from "./PlayAllWeeksButton.vue";
import PlayNextWeekButton from "./PlayNextWeekButton.vue";
import ResetDataButton from "./ResetDataButton.vue";
import Fixtures from "./Fixtures.vue";
import {reactive, ref, toValue, watch, onUnmounted} from "vue";
import Winner from "./Winner.vue";
import Logo from "./Logo.vue";
import Predictions from "./Predictions.vue";
import confetti from 'canvas-confetti';
import {useDebounceFn} from "@vueuse/core";
import FixturesModal from "@/components/FixturesModal.vue";
const emit = defineEmits(['onReset']);
const props = defineProps(['league']);

const league = reactive({
    data: props.league
});
const fixturesRef = ref();
const fixtures2Ref = ref();
const tableRef = ref();
const predictionsRef = ref();
const fixturesModalOpen = ref(false);

const onResetData = () => {
    tableRef.value.mutate();
    emit('onReset');
}

const refreshAll = (leagueData) => {
    fixtures2Ref.value.mutate();
    fixturesRef.value.mutate();
    tableRef.value.mutate();
    if (leagueData.data.round > 2) {
        predictionsRef.value.mutate();
    }
}
const onWeekPlayed = (newLeagueData) => {
    refreshAll(newLeagueData)
    league.data = newLeagueData.data;
}
</script>

<template>
    <div :class="['outer-wrapper', fixturesModalOpen && 'backdrop']">
        <div class="actions" ref="actions">
            <PlayNextWeekButton @week-played="onWeekPlayed" v-if="!league.data.hasAllPlayed"/>
            <PlayAllWeeksButton @allWeeksPlayed="onWeekPlayed" v-if="!league.data.hasAllPlayed"/>
            <ResetDataButton @reset="onResetData"/>
        </div>
        <div class="inner-wrapper">
            <div class="score-table block">
                <div class="flex items-center gap-4 mb-2">
                    <h2>Standings</h2>
                    <button class="view-all-fixtures-btn" v-if="league.data.hasAllPlayed" @click="fixturesModalOpen = true">View All</button>
                </div>
                <hr class="mb-2"/>
                <LeagueTable ref="tableRef"/>
            </div>
            <div class="fixtures block">
                <template v-if="league.data.hasAllPlayed">
                    <Winner :team="league.data.winner" class="!min-h-72 lg:!min-h-80"/>
                </template>
                <template v-else>
                    <Fixtures ref="fixturesRef" endpoint="/api/fixtures"/>
                    <Fixtures ref="fixtures2Ref" endpoint="/api/fixtures/past-week" @goal-changed="refreshAll">
                        <div class="flex items-center gap-4 mt-6">
                            <h3 class="text-xl">Past Week</h3>
                            <button class="view-all-fixtures-btn" v-if="league.data.round > 1" @click="fixturesModalOpen = true">View All</button>
                        </div>
                        <hr class="mb-2"/>
                    </Fixtures>
                    <span class="mt-4 text-gray-400 text-xs">You can edit scores by clicking</span>
                </template>
            </div>
            <div class="predictions block">
                <Predictions ref="predictionsRef" />
            </div>
        </div>
        <FixturesModal v-if="fixturesModalOpen" :open="fixturesModalOpen" @on-close="fixturesModalOpen = false" />
        <Logo :style="{
                position: 'fixed',
    left: '43%',
    bottom: 0,
    'z-index': -1
        }"/>
    </div>
</template>

<style scoped>
.outer-wrapper {
    @apply pt-10 lg:pt-20;
}

.inner-wrapper {
    @apply rounded-lg grid grid-cols-1 xl:grid-cols-[3fr,2fr,2fr] gap-4 mx-auto px-5;
}

.actions {
    @apply rounded-lg flex mx-auto mb-4 gap-4 flex-wrap px-5;
}

.actions button {
    @apply bg-white text-black hover:bg-gray-50  font-bold py-2 px-4 rounded flex gap-2 items-center justify-center;
}

.inner-wrapper .block {
    @apply p-4 min-h-60;
}

.score-table {
    @apply bg-white rounded-lg h-full;
}

.fixtures {
    @apply bg-white rounded-lg h-full;
}

.predictions {
    @apply bg-white rounded-lg h-full;
}

h2 {
    font-family: Premier-Sans-Heavy;
    @apply text-3xl font-semibold;
}

.view-all-fixtures-btn {
    background: var(--theme-color);
    color: white;
    @apply px-3 py-1 rounded-md text-sm;
}
</style>
