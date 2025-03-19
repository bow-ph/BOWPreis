import template from './errors.html.twig';

const { Component  } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('bow-preishoheit-errors', {
    template,

    inject: ['repositoryFactory'],

    props: {
        product: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            errors: [],
            isLoading: false,
            total: 0,
            page: 1,
            limit: 25,
            sortBy: 'createdAt',
            sortDirection: 'DESC'
        };
    },

    computed: {
        errorLogRepository() {
            return this.repositoryFactory.create('bow_preishoheit_error_log');
        },

        columns() {
            return [
                {
                    property: 'errorType',
                    label: this.$tc('bow-preishoheit.errors.columnErrorType'),
                    primary: true
                },
                {
                    property: 'errorMessage',
                    label: this.$tc('bow-preishoheit.errors.columnErrorMessage')
                },
                {
                    property: 'createdAt',
                    label: this.$tc('bow-preishoheit.errors.columnDate')
                }
            ];
        }
    },

    created() {
        this.loadErrorLogs();
    },

    methods: {
        loadErrorLogs() {
            this.isLoading = true;

            const criteria = new Criteria(this.page, this.limit);
            criteria.addFilter(Criteria.equals('productId', this.product.id));
            criteria.addSorting(Criteria.sort(this.sortBy, 'DESC'));

            return this.errorLogRepository.search(criteria, Shopware.Context.api)
                .then(result => {
                    this.errors = result.items;
                    this.total = result.total;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onPageChange({ page, limit }) {
            this.page = page;
            this.limit = limit;
            this.loadErrorLogs();
        },

        onSort({ sortBy, sortDirection }) {
            this.sortBy = sortBy;
            this.sortDirection = sortDirection;
            this.loadErrorLogs();
        }
    },

    computed: {
        errorLogRepository() {
            return this.repositoryFactory.create('bow_preishoheit_error_log');
        },

        columns() {
            return [
                {
                    property: 'errorType',
                    label: this.$tc('bow-preishoheit.errors.columnErrorType'),
                    primary: true
                },
                {
                    property: 'errorMessage',
                    label: this.$tc('bow-preishoheit.errors.columnErrorMessage')
                },
                {
                    property: 'createdAt',
                    label: this.$tc('bow-preishoheit.errors.columnDateTime')
                }
            ];
        }
    }
});
