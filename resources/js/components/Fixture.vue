<script setup>
import Team from "./Team.vue";
import {ref} from "vue";
import { watchDebounced } from '@vueuse/core'

const props = defineProps(['homeTeam', 'awayTeam', 'round', 'homeTeamStats', 'awayTeamStats', 'teamProps', 'freeze']);
const emit = defineEmits(['goalChanged']);

const homeGoals = ref(props.homeTeamStats?.goals);
const awayGoals = ref(props.awayTeamStats?.goals);
const updateData = (statId, goals) => {
    if (props.freeze ?? false) {
        return;
    }
    fetch('/api/fixtures/stats/' + statId, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            goals
        })
    }).then(() => {
        emit('goalChanged')
    })
}
watchDebounced(homeGoals, () => {
    updateData(props.homeTeamStats.id, homeGoals.value);
}, { debounce: 1000 });

watchDebounced(awayGoals, () => {
    updateData(props.awayTeamStats.id, awayGoals.value);
}, { debounce: 1000 });
</script>

<template>
<div class="grid grid-cols-[2fr,1fr,2fr] items-center">
    <div class="team justify-self-start">
        <Team :team="homeTeam" v-bind="teamProps" />
    </div>
    <div class="text-center justify-self-center text-lg">
        <span v-if="typeof homeTeamStats?.goals == 'number'" :contenteditable="!(freeze || false)" @input="e => !isNaN(parseInt(e.target.innerText, 10)) ? homeGoals = parseInt(e.target.innerText, 10) : {}">
            {{ homeGoals }}
        </span>
        -
        <span v-if="typeof awayTeamStats?.goals == 'number'" :contenteditable="!(freeze || false)" @input="e => !isNaN(parseInt(e.target.innerText, 10)) ? awayGoals = parseInt(e.target.innerText, 10) : {}">
            {{ awayGoals }}
        </span>
    </div>
    <div class="team justify-self-end">
        <Team :team="awayTeam" v-bind="teamProps" />
    </div>
</div>
</template>

<style scoped>
.team {
    font-family: Premier-Sans-Bold;
}
</style>
