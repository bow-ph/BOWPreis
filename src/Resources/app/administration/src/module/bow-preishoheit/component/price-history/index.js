import template from './price-history.html.twig';
import './price-history.scss';

const { Component, Mixin } = Shopware;
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
            history: [],
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
            columns: [
                {
                    property: 'ean',
                    label: this.$tc('bow-preishoheit.history.columnEan'),
                    primary: true
                },
                {
                    property: 'productName',
                    label: this.$tc('bow-preishoheit.history.columnProductName')
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

        priceHistoryCriteria() {
            const criteria = new Criteria(this.page, this.limit);
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

            const criteria = this.priceHistoryCriteria;
            
            if (this.dateRange.start) {
                criteria.addFilter(Criteria.range('createdAt', {
                    gte: this.dateRange.start
                }));
            }
            
            if (this.dateRange.end) {
                criteria.addFilter(Criteria.range('createdAt', {
                    lte: this.dateRange.end
                }));
            }

            return this.priceHistoryRepository.search(criteria)
                .then((result) => {
                    this.history = result.items;
                    this.total = result.total;
                })
                .catch((error) => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.history.errorTitle'),
                        message: error.message
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

        onSort({ sortBy, sortDirection }) {
            this.sortBy = sortBy;
            this.sortDirection = sortDirection;
            this.loadHistory();
        },

        onDateRangeChange() {
            this.loadHistory();
        },

        getPriceClass(item) {
            if (item.newPrice <= 0) {
                return 'price--error';
            }
            if (item.newPrice < item.oldPrice * 0.5) {
                return 'price--warning';
            }
            return '';
        }
    }
});
