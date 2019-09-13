<script>
	<?php
	ob_start();
	include STM_LMS_PATH . '/post_type/metaboxes/components/manage_post_type.php';
	$template = preg_replace("/\r|\n/", "", addslashes(ob_get_clean()));
	?>

    Vue.component('stm-manage-post-type', {
        props: ['post_type', 'meta_key'],
        data: function () {
            return {
                posts: [],
                pages: [],
                current_page: 1,
                total: 0,
                filter: '',
                loading: false,
            }
        },
        mounted: function () {
            var _this = this;
            _this.getPosts();

            STM_LMS_EventBus.$on('stm_lms_udemy_course_imported', function(){
                _this.getPosts();
            });
        },
        template: '<?php echo $template; ?>',
        methods: {
            getPosts() {
                var _this = this;
                var url = stm_lms_ajaxurl + '?action=stm_manage_posts';
                url += '&post_types=' + _this.post_type;
                url += '&page=' + _this.current_page;
                url += '&post_status=' + _this.filter;
                url += '&meta=' + _this.meta_key;
                _this.loading = true;

                _this.$http.get(url).then(function (r) {
                    _this.posts = r.body['posts'];
                    _this.total = Math.ceil(r.body.total / r.body.per_page);
                    _this.pagination();
                    _this.loading = false;
                })
            },
            pagination() {
                this.pages = [];
                var i = 0;
                while (i < this.total) {
                    i++;
                    this.pages.push(i);
                }
            },
            switchPage(page) {
                this.current_page = page;
                this.getPosts();
            },
            switchStatus() {
                this.current_page = 1;
                this.getPosts();
            },
            updateCourse(post_id) {
                console.log(post_id);
            },
            changeStatus(index, post_id, status) {
                var _this = this;

                _this.$set(_this.posts[index], 'loading', true);

                _this.$http.get(stm_lms_ajaxurl + '?action=stm_lms_change_post_status&post_id=' + post_id + '&status=' + status).then(function (r) {
                    r = r.body;

                    _this.$set(_this.posts[index], 'loading', false);
                    _this.$set(_this.posts[index], 'status', r);
                });
            }
        },
    })
</script>