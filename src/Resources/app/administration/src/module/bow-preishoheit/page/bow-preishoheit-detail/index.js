import template from './bow-preishoheit-job-detail.html.twig';
const { Component, Mixin } = Shopware;

Component.register('bow-preishoheit-job-detail', {
    template,

    inject: ['repositoryFactory', 'httpClient'],

    mixins: [Mixin.getByName('notification')],

    props: {
        jobId: {
            type: String,
            required: true
        }
    },

    data() {
        return {
            isLoading: false,
            jobStatus: null,
            jobData: null,
            pollingInterval: null
        };
    },

    computed: {
        isJobFinished() {
            return this.jobStatus === 'Finished';
        },
        isJobPending() {
            return this.jobStatus === 'Pending';
        },
        isJobFailed() {
            return this.jobStatus === 'Failed';
        }
    },

    created() {
        this.loadJobStatus();
        this.startPolling();
    },

    beforeUnmount() {
        clearInterval(this.pollingInterval);
    },

    methods: {
        loadJobStatus() {
            this.isLoading = true;
            this.$http.get(`/api/_action/bow-preishoheit/jobs/${this.jobId}`)
                .then(response => {
                    if (response.data.success) {
                        this.jobStatus = response.data.status;
                        this.jobData = response.data.data;
                    } else {
                        this.createNotificationError({
                            title: this.$tc('global.default.error'),
                            message: response.data.message
                        });
                    }
                })
                .catch(error => {
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: error.message
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        startPolling() {
            this.pollingInterval = setInterval(() => {
                if (!this.isJobFinished && !this.isJobFailed) {
                    this.loadJobStatus();
                } else {
                    clearInterval(this.pollingInterval);
                }
            }, 30000); // alle 30 Sekunden
        },

        refreshStatus() {
            this.loadJobStatus();
        }
    }
});