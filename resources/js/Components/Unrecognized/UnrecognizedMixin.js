import includes from 'lodash/includes';

export default {
    props: {
        admissions: Array,
        heading: {
            default: null
        },
    },
    data() {
        return {
            loading: true,
            mutableAdmissions: this.admissions
        };
    },
    computed: {
        hasAccount () {
            return includes(Object.keys(this.$props || {}), 'account');
        }
    },
    methods: {
        deleteRow(index) {
            this.mutableAdmissions.splice(index, 1);
        },
        deleteRows(index) {
            let oldCommonName = this.mutableAdmissions[index].patient.common_name;

            this.mutableAdmissions = this.mutableAdmissions.filter(row => {
                return row.patient.common_name.toLowerCase() !== oldCommonName.toLowerCase();
            });
        }
    }
};
