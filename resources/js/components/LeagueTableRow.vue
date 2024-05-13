<script setup>
import Team from "./Team.vue";

defineProps(['rank','team', 'goals', 'points', 'wins', 'draws', 'loses', 'fouls', 'matches', 'goal_difference']);
</script>

<template>
<tr>
    <td class="team-name">
        <Team :team="team">
            {{ rank }}
        </Team>
    </td>
    <td class="matches">{{ matches.toString() }}</td>
    <td class="wins">{{ wins.toString() }}</td>
    <td class="draws">{{ draws.toString() }}</td>
    <td class="loses">{{ loses.toString()  }}</td>
    <td class="goals">{{ goals.toString()  }}</td>
    <td class="gd">{{ goal_difference.toString()  }}</td>
    <td class="points">{{ points.toString()  }}</td>
</tr>
</template>

<style scoped>
tr {
    font-family: Premier-Sans-Regular, sans-serif;
    font-size: 18px;
}
.team-name {
    padding-right: 40px;
}
</style>
