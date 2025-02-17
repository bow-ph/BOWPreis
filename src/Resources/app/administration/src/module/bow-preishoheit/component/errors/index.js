import template from './errors.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

export default {
    template,

    inject: [
        'repositoryFactory'
    ],

    props: {
        product: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            isLoading: false,
            errorLogs: [],
            total: 0,
            page: 1,
            limit: 25
        };
    },

    computed: {
        errorLogRepository() {
            return this.repositoryFactory.create('bow_preishoheit_error_log');
        },

        errorLogColumns() {
            return [{
                property: 'errorType',
                label: this.$tc('bow-preishoheit.detail.columnErrorType'),
                primary: true
            }, {
                property: 'errorMessage',
                label: this.$tc('bow-preishoheit.detail.columnErrorMessage')
            }, {
                property: 'createdAt',
                label: this.$tc('bow-preishoheit.detail.columnDate')
            }];
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
            criteria.addSorting(Criteria.sort('createdAt', 'DESC'));

            return this.errorLogRepository.search(criteria)
                .then(({ items, total }) => {
                    this.errorLogs = items;
                    this.total = total;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onPageChange({ page, limit }) {
            this.page = page;
            this.limit = limit;
            this.loadErrorLogs();
        }
    }
};
