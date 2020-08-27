<template>
    <card class="card relative card-panel">
        <loading-view :loading="initialLoading">
            <div class="px-6 py-4" :loading="loading">

                <h3 class="flex mb-3 text-base text-80 font-bold">
                    Import Modules stats
                </h3>

                <div class="overflow-hidden overflow-y-auto max-h-90px">
                    <ul class="list-reset">

                        <template v-for="stat in stats">
                            <li class="text-xs text-80 leading-normal">
                                <span class="inline-block rounded-full w-2 h-2 mr-2" v-bind:class="{
                                    'color-success': stat.status == 'Finished',
                                    'color-danger': stat.status == 'Started' || stat.status == 'Error' || stat.status == ''
                                }"></span>
                                <strong>{{ stat.module }}</strong> Executed: {{ stat.last_run }}
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </loading-view>
    </card>
</template>

<script>
export default {
    data() {
        return {
            stats: {},
            initialLoading: true,
            loading: false
        }
    },
    mounted() {
        Nova.request().get("/nova-vendor/import-stats/endpoint")
        .then(({ data }) => {
            this.stats = data.stats;
            this.initialLoading = false;
            this.loading = false;
        })
    }
}
</script>