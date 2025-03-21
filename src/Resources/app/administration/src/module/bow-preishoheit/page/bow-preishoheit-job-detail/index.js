import template from './bow-preishoheit-job-detail.html.twig';

const { Component } = Shopware;

Component.register('bow-preishoheit-job-detail', {
    template,

    inject: ['notificationService'],

    props: {
        jobId: {
            type: String,
            required: true
        }
    },

    data() {
        return {
            job: null,
            isLoading: false
        };
    },

    created() {
        this.loadJob();
    },

    methods: {
        loadJob() {
            this.isLoading = true;

            Shopware.Service('httpClient').get(`/api/_action/bow-preishoheit/jobs/${this.jobId}`)
                .then(response => {
                    this.job = response.data.data;
                })
                .catch(error => {
                    this.notificationService.error(
                        error.response?.data?.message || error.message
                    );
                    this.$router.push({ name: 'bow.preishoheit.jobList' });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onBack() {
            this.$router.push({ name: 'bow.preishoheit.jobList' });
        }
    }
});
