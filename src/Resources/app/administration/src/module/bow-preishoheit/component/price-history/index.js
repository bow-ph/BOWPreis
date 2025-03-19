import template from './price-history.html.twig';
import './price-history.scss';

const { Component, Mixin, Application } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('bow-preishoheit-price-history', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            historyItems: [],
            isLoading: false,
            page: 1,
            limit: 25,
            total: 0,
            sortBy: 'createdAt',
            sortDirection: 'DESC',
            dateRange: {
                start: null,
                end: null
            },
            isExporting: false,
            showExportModal: false,
            columns: [
                {
                    property: 'ean',
                    label: this.$tc('bow-preishoheit.history.columnEan')
                },
                {
                    property: 'oldPrice',
                    label: this.$tc('bow-preishoheit.history.columnOldPrice')
                },
                {
                    property: 'newPrice',
                    label: this.$tc('bow-preishoheit.history.columnNewPrice')
                },
                {
                    property: 'createdAt',
                    label: this.$tc('bow-preishoheit.history.columnDateTime')
                }
            ]
        };
    },

    computed: {
        priceHistoryRepository() {
            return this.repositoryFactory.create('bow_preishoheit_price_history');
        },

        historyCriteria() {
            const criteria = new Criteria(this.page, this.limit);

            if (this.dateRange.start || this.dateRange.end) {
                criteria.addFilter(Criteria.range('createdAt', {
                    gte: this.dateRange.start,
                    lte: this.dateRange.end
                }));
            }

            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));
            return criteria;
        }
    },

    created() {
        this.loadHistory();
    },

    methods: {
        loadHistory() {
            this.isLoading = true;

            return this.priceHistoryRepository.search(this.historyCriteria, Shopware.Context.api)
                .then(result => {
                    this.priceHistory = result.items;
                    this.total = result.total;
                })
                .catch(error => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.history.errorTitle'),
                        message: error.message || this.$tc('bow-preishoheit.history.loadError')
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onPageChange({ page, limit }) {
            this.page = page;
            this.limit = limit;
            this.loadHistory();
        },

        onDateRangeChange() {
            this.page = 1;
            this.loadHistory();
        },

        getPriceClass(item) {
            if (item.newPrice <= 0) return 'price--error';
            if (item.newPrice < item.oldPrice * 0.5) return 'price--warning';
            return '';
        },

        async onExportClick() {
            this.isExporting = true;

            try {
                const response = await Application.getContainer('init').httpClient.post(
                    '/_action/bow-preishoheit/export-history',
                    { dateRange: this.dateRange },
                    { responseType: 'blob' }
                );

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'price-history-export.csv');
                document.body.appendChild(link);
                link.click();
                window.URL.revokeObjectURL(url);

                this.createNotificationSuccess({
                    title: this.$tc('bow-preishoheit.history.exportSuccessTitle'),
                    message: this.$tc('bow-preishoheit.history.exportSuccessMessage')
                });
            } catch (error) {
                this.createNotificationError({
                    title: this.$tc('bow-preishoheit.history.exportErrorTitle'),
                    message: error.message || this.$tc('bow-preishoheit.history.exportError')
                });
            } finally {
                this.isExporting = false;
            }
        }
    }
});
