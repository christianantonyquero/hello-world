// Mixin for an error array and a log method to handle axios and custom errors
export default {
    data() {
        return {
            errors: [],
        }
    },
    methods: {
        logError(msg, error) {
            let err_msg = msg;
            console.log(error);
            if (error && error.response && error.response.data.message) {
                err_msg += ' - ' + error.response.data.message;
            }
            this.errors.push(err_msg);
            if (error && error.response && error.response.data.errors) {
                for (const prop in error.response.data.errors) {
                    this.errors.push(error.response.data.errors[prop][0]);
                }
            }
        }
    }
}