export default {
    data() {
        return {
            errors: [],
        }
    },
    methods: {
        logAxiosError(error) {
            if(error.response) {
                if(error.response.data.error_msg) {
                    this.errors.push(error.response.data.error_msg);
                }
                if(error.response.data.message) {
                    this.errors.push(error.response.data.message);
                }
                for( const prop in error.response.data.errors) {
                    this.errors.push(error.response.data.errors[prop][0]);
                }
            }
        }
    }
}