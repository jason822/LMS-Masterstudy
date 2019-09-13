(function ($) {

    $(document).ready(function () {

        new Vue({
            el: '#my-courses',
            data: function () {
                return {
                    loading: false,
                    courses: [],
                    offset: 0,
                    total: false
                }
            },
            mounted: function () {
                this.getCourses();
            },
            methods: {
                getCourses() {
                    var vm = this;
                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_user_courses&offset=' + vm.offset;
                    vm.loading = true;

                    this.$http.get(url).then(function (response) {
                        if(response.body['posts']) {
                            response.body['posts'].forEach(function (course) {
                                vm.courses.push(course);
                            });
                        }
                        vm.total = response.body['total'];
                        vm.loading = false;
                        vm.offset++;
                    });
                }
            }
        });

        new Vue({
            el: '#my-quizzes',
            data: function () {
                return {
                    loading: false,
                    quizzes: [],
                    offset: 0,
                    total: false
                }
            },
            mounted: function () {
                this.getQuizzes();
            },
            methods: {
                getQuizzes() {
                    var vm = this;
                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_user_quizzes&offset=' + vm.offset;
                    vm.loading = true;

                    this.$http.get(url).then(function (response) {
                        if(response.body['posts']) {
                            response.body['posts'].forEach(function (course) {
                                vm.quizzes.push(course);
                            });
                        }
                        vm.total = response.body['total'];
                        vm.loading = false;
                        vm.offset++;
                    });
                }
            }
        });

        new Vue({
            el: '#my-orders',
            data: function () {
                return {
                    loading: false,
                    orders: [],
                    offset: 0,
                    total: false
                }
            },
            mounted: function () {
                this.getOrders();
            },
            methods: {
                getOrders() {
                    var vm = this;
                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_user_orders&offset=' + vm.offset;
                    vm.loading = true;

                    this.$http.get(url).then(function (response) {
                        if(response.body['posts']) {
                            response.body['posts'].forEach(function (order) {
                                vm.orders.push(order);
                            });
                        }
                        vm.total = response.body['total'];
                        vm.loading = false;
                        vm.offset++;
                    });
                },
                openTab(key) {
                    var opened = (typeof this.orders[key]['isOpened'] === 'undefined') ? true : !this.orders[key]['isOpened'];
                    this.$set(this.orders[key], 'isOpened', opened);
                }
            }
        });

    });

    $(window).load(function() {
        var hash = window.location.hash;
        if(hash === '#settings') {
            $('.stm-lms-user_edit_profile_btn').click();
        }
    })

})(jQuery);
