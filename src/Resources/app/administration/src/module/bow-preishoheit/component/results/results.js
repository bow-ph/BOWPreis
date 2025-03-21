import template from './results-tab.html.twig';
import { Component, Mixin } from 'src/core/shopware';

Component.register('bow-preishoheit-results-tab', {
    template,

    inject: ['BowPreishoheitApiService'],

    mixins: [Mixin.getByName('notification')],

    data() {
        return {
            results: [],
            editingResult: null,
            changedResults: [],
            isSaving: false,
            columns: [
                { property: 'jobId', label: 'Job ID' },
                { property: 'price', label: 'Price' },
                { property: 'title', label: 'Title' },
                { property: 'ean', label: 'EAN' },
                { property: 'actions', label: 'Actions', allowResize: false }
            ]
        };
    },

    created() {
        this.loadResults();
    },

    methods: {
        loadResults() {
            this.BowPreishoheitApiService.getResults().then(({ data }) => {
                this.results = data;
            });
        },

        editResult(item) {
            const existing = this.changedResults.find(r => r.jobId === item.jobId);
            if (existing) {
                this.editingResult = existing;
            } else {
                this.editingResult = { ...item };
                this.changedResults.push(this.editingResult);
            }
        },

        saveAllChanges() {
            if (this.changedResults.length === 0) {
                this.createNotificationWarning({ message: 'No changes to save.' });
                return;
            }

            this.isSaving = true;

            this.BowPreishoheitApiService.saveApprovedResults(this.changedResults)
                .then(() => {
                    this.createNotificationSuccess({ message: 'All changes approved and saved!' });
                    this.changedResults = [];
                    this.loadResults();
                })
                .catch(() => {
                    this.createNotificationError({ message: 'Failed to save approved results!' });
                })
                .finally(() => {
                    this.isSaving = false;
                });
        }
    }
});
