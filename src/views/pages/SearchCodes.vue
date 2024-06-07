<script>
// pinia
import { mapStores } from 'pinia';
import { useResultsStore } from '@/store/resultsStore';
import Results from '@/components/Results.vue';
 
export default {
    components: {
        Results,
    },
    computed: {
        ...mapStores(
            useResultsStore,
        ),
    },
    mounted() {
        console.log('Search Codes page mounted...', {searchCodes: this.resultsStore.searchcodes});
    }
}
 
</script>
 
<template>
    <h1>Search Code Results</h1>

    <!-- add component -->

</template>