import template from './bow-preishoheit-job-list.html.twig';

const { Component } = Shopware;

Component.register('bow-preishoheit-job-list', {
    template,

    inject: ['notificationService'],

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
                this.notificationService.error('httpClient Service ist nicht verfügbar.');
                this.isLoading = false;
                return;
            }

            httpClient.get('/api/bow-preishoheit/jobs')
                .then(response => {
                    this.jobs = response.data.data || [];
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
        },

        openJobDetail(item) {
            this.$router.push({
                name: 'bow.preishoheit.jobDetail',
                params: { jobId: item.id }
            });
        },

        getBadgeVariant(status) {
            switch (status.toLowerCase()) {
                case 'finished':
                case 'success':
                    return 'success';
                case 'pending':
                case 'running':
                    return 'warning';
                case 'failed':
                case 'error':
                    return 'danger';
                default:
                    return 'neutral';
            }
        }
    }
});
