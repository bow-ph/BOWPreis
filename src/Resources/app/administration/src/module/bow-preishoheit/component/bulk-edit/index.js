import template from './bulk-edit.html.twig';
import './bulk-edit.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('bow-preishoheit-bulk-edit', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            products: [],
            selectedItems: [],
            isLoading: false,
            isSaving: false,
            showBulkEditModal: false,
            processingStatus: {
                total: 0,
                processed: 0,
                success: 0,
                failed: 0
            },
            page: 1,
            limit: 25,
            total: 0,
            sortBy: 'name',
            sortDirection: 'ASC',
            bulkEditData: {
                adjustmentType: 'percentage',
                value: null
            },
            columns: [
                {
                    property: 'name',
                    label: this.$tc('bow-preishoheit.bulk.columnName'),
                    primary: true
                },
                {
                    property: 'productNumber',
                    label: this.$tc('bow-preishoheit.bulk.columnProductNumber')
                },
                {
                    property: 'price',
                    label: this.$tc('bow-preishoheit.bulk.columnPrice')
                },
                {
                    property: 'surchargePercentage',
                    label: this.$tc('bow-preishoheit.bulk.columnSurcharge')
                }
            ],
            adjustmentTypeOptions: [
                {
                    value: 'percentage',
                    label: this.$tc('bow-preishoheit.bulk.adjustmentTypePercentage')
                },
                {
                    value: 'fixed',
                    label: this.$tc('bow-preishoheit.bulk.adjustmentTypeFixed')
                }
            ]
        };
    },

    computed: {
        productRepository() {
            return this.repositoryFactory.create('product');
        },

        productCriteria() {
            const criteria = new Criteria(this.page, this.limit);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));
            return criteria;
        },

        isValidBulkEdit() {
            return this.bulkEditData.adjustmentType &&
                   this.bulkEditData.value !== null &&
                   this.bulkEditData.value >= 0 &&
                   this.bulkEditData.value <= 100;
        }
    },

    created() {
        this.loadProducts();
    },

    methods: {
        loadProducts() {
            this.isLoading = true;

            return this.productRepository.search(this.productCriteria)
                .then((result) => {
                    this.products = result.items;
                    this.total = result.total;
                })
                .catch((error) => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.bulk.errorTitle'),
                        message: error.message
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onSelectionChange(selection) {
            this.selectedItems = Object.values(selection);
        },

        onPageChange({ page, limit }) {
            this.page = page;
            this.limit = limit;
            this.loadProducts();
        },

        onSort({ sortBy, sortDirection }) {
            this.sortBy = sortBy;
            this.sortDirection = sortDirection;
            this.loadProducts();
        },

        onBulkEditClick() {
            this.showBulkEditModal = true;
        },

        onCloseBulkEditModal() {
            this.showBulkEditModal = false;
            this.isSaving = false;
            this.bulkEditData = {
                adjustmentType: 'percentage',
                value: null
            };
        },

        onClearSelection() {
            this.selectedItems = [];
        },

        async onSaveBulkEdit() {
            if (!this.isValidBulkEdit) {
                return;
            }

            this.isSaving = true;

            try {
                const updates = this.selectedItems.map(item => ({
                    id: item.id,
                    surchargePercentage: this.bulkEditData.adjustmentType === 'percentage'
                        ? this.bulkEditData.value
                        : this.calculatePercentageFromFixed(item.price, this.bulkEditData.value)
                }));

                await this.productRepository.saveAll(updates);

                this.createNotificationSuccess({
                    title: this.$tc('bow-preishoheit.bulk.successTitle'),
                    message: this.$tc('bow-preishoheit.bulk.successMessage')
                });

                this.onCloseBulkEditModal();
                this.loadProducts();
            } catch (error) {
                this.createNotificationError({
                    title: this.$tc('bow-preishoheit.bulk.errorTitle'),
                    message: error.message
                });
            } finally {
                this.isSaving = false;
            }
        },

        calculatePercentageFromFixed(price, fixedValue) {
            if (!price || price <= 0) {
                return 0;
            }
            return (fixedValue / price) * 100;
        }
    }
});
