<template>
  <form id="create-user-app" @submit="createUser" action="/admin/user" method="post">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="row">
            <div class="col-md">
              <h2>Create New User</h2>
            </div>
          </div>
          <hr />
          <errors v-bind:errors="errors"></errors>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="user_name">Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="user_name"
                  name="user_name"
                  v-model="name"
                  aria-describedby="name"
                  placeholder="Name"
                  required
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="user_email">Email</label>
                <input
                  type="text"
                  class="form-control"
                  id="user_email"
                  name="user_email"
                  v-model="email"
                  aria-describedby="email"
                  placeholder="Email"
                  required
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="password">Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  name="password"
                  v-model="password"
                  aria-describedby="password"
                  required
                />
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group form-check"> 
                <input type="checkbox" class="form-check-input" id="is_admin" 
                       v-model="is_admin"/>
                <label for="is_admin">Is Admin</label>
                <small class="form-text text-muted">Can user access the admin.</small>
               
              </div>
           
              <div class="form-group form-check"> 
                <input type="checkbox" class="form-check-input" id="is_tester" 
                       v-model="is_tester"/>
                <label for="is_tester">Is Test User</label>
                <small class="form-text text-muted">Can user access closed/test stores.</small>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="create-user">Create User</button>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
export default {
  data() {
    return {
      name: "",
      email: "",
      password: "",
      is_admin: false,
      is_tester: false,
      errors: []
    };
  },
  methods: {
    createUser: function(e) {
      e.preventDefault();
      this.errors = [];
      return axios
        .post("/admin/users", {
            name: this.name,
            email: this.email,
            password: this.password,
            is_admin: this.is_admin,
            is_tester: this.is_tester
        })
        .then((response) => {
          window.location.href = "/admin/users";
        })
        .catch((error) => {
            let err_msg = 'Error Saving User';
            console.log(error);
            if( error.response && error.response.data.message) {
                err_msg += ' - ' + error.response.data.message;
            }
            this.errors.push(err_msg);
            if(error.response && error.response.data.errors) {
                for( const prop in error.response.data.errors) {
                    this.errors.push(error.response.data.errors[prop][0]);
                }
            }
        });
    }
  }
};
</script>
