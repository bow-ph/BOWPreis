import template from './bow-preishoheit-job-list.html.twig';

const { Component } = Shopware;

Component.register('bow-preishoheit-job-list', {
    template,

    inject: ['repositoryFactory', 'notificationService'],

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

            const httpClient = Shopware.Service('httpClient');

            if (!httpClient) {
                this.notificationService.error('httpClient konnte nicht geladen werden.');
                this.isLoading = false;
                return;
            }

            httpClient.get('/api/bow-preishoheit/jobs')
                .then(response => {
                    this.jobs = response.data.data;
                })
                .catch(error => {
                    this.notificationService.error(
                        error.response?.data?.message || error.message
                    );
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onRefresh() {
            this.loadJobs();
        }
    }
});
