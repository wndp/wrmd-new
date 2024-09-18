import CommonName from '@/Components/FormElements/CommonName.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import includes from 'lodash/includes';

export default {
    components: {
        CommonName,
        PrimaryButton,
        SecondaryButton
    },
    props: ['row', 'index'],
    data() {
        return {
            updating: false,
            success: null,
            message: '',
            newCommonName: ''
        };
    },
    computed: {
        onlyOneAccount () {
            return includes(Object.keys(this.$parent.$props || {}), 'account');
        }
    },
    methods: {
        update: function () {
            this.updating = true;

            window.axios.put(`/internal-api/unrecognized-patients/patient/${this.row.patient_id}`, {
                newCommonName: this.newCommonName
            })
                .then(response => {
                    this.success = true;
                    this.message = response.data.message;
                    this.updating = false;

                    setTimeout(() => {
                        this.$emit('updated', this.index);
                    }, 1500);
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.success = false;
                        this.message = error.response.data.message;
                        this.updating = false;
                    }
                });
        },
        updateAll: function(account) {
            window.axios.put(`/internal-api/unrecognized-patients/account/${account.id}`, {
                    taxon_id: this.row.patient.taxon_id,
                    oldCommonName: this.row.patient.common_name,
                    newCommonName: this.newCommonName
                })
                .then(response => {
                    this.success = true;
                    this.message = response.data.message;
                    this.updating = false;

                    setTimeout(() => {
                        this.$emit('updatedAll', this.index);
                    }, 1500);
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.success = false;
                        this.message = error.response.data.message;
                        this.updating = false;
                    }
            });
        }
    }
};
