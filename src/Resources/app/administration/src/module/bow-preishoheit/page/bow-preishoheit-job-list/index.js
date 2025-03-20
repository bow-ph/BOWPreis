import template from './bow-preishoheit-job-list.html.twig';

const { Component } = Shopware;

Component.register('bow-preishoheit-job-list', {
    template,

    inject: ['httpClient'],

    data() {
        return {
            isLoading: false,
            jobs: []
        };
    },

    created() {
        this.loadJobs();
    },

    methods: {
        loadJobs() {
            this.isLoading = true;

            this.httpClient.get('/api/bow-preishoheit/jobs')
                .then(response => {
                    this.jobs = response.data.data;
                })
                .catch(error => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.jobs.errorTitle'),
                        message: error.message
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
});
