<script setup>
import LeagueTableRow from "./LeagueTableRow.vue";
import {onMounted, reactive} from "vue";

const state = reactive({
    data: null,
    error: null,
    loading: true
});

const fetchData = () => {
    fetch('/api/league/table')
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
        Loading...
    </div>
    <div v-else-if="state.error">
        Error: {{ state.error }}
    </div>
    <div v-else>
        <table>
            <tr>
                <th>Team</th>
                <th>MP</th>
                <th>W</th>
                <th>D</th>
                <th>L</th>
                <th>G</th>
                <th>GD</th>
                <th>Pts</th>
            </tr>
            <LeagueTableRow
                v-for="(row, index) in state.data.data.rows"
                :key="row.id"
                :rank="index + 1"
                v-bind="row"
            />
        </table>
    </div>
</template>

<style scoped>
table, tr, th, td {
    text-align: left;
    border-collapse: separate;
    border-spacing: 10px;
}

th, td {
    padding-right: 20px;
}

th {
    font-family: Premier-Sans-Bold;
}
</style>
