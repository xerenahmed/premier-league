import {toRefs,watchEffect, toValue, reactive} from "vue";

const useFetch = async (url) => {
    const state = reactive({
        data: null,
        error: null,
        loading: false
    });

    watchEffect(() => {
        state.loading = true;

        fetch(toValue(url))
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
    });

    return {...toRefs(state)}
}

export default useFetch
