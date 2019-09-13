<div>
    <div class="stm_lms_manage_post_type" v-bind:class="{'loading' : loading}">

        <div class="stm_lms_manage_post_type__filter">
            <div class="pagination" v-if="total > 1">
                <a href="#"
                   v-for="page in pages"
                   v-on:click.prevent="switchPage(page)"
                   v-bind:class="{'active' : page == current_page}">{{page}}</a>
            </div>

            <div class="filter">
                <select v-model="filter" v-on:change="switchStatus()">
                    <option value=""><?php esc_html_e('All', 'masterstudy'); ?></option>
                    <option value="publish"><?php esc_html_e('Published', 'masterstudy'); ?></option>
                    <option value="draft"><?php esc_html_e('Draft', 'masterstudy'); ?></option>
                    <option value="trash"><?php esc_html_e('Trash', 'masterstudy'); ?></option>
                </select>
            </div>
        </div>

        <table>
            <thead>
            <tr>
                <th><?php esc_html_e('Course', 'masterstudy'); ?></th>
                <th class="stm_lms_manage_post_type__actions"><?php esc_html_e('Manage actions', 'masterstudy'); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(post, index) in posts" v-bind:class="{'loading' : post.loading}">
                <td>
                    <a v-bind:href="post.url" v-html="post.title" target="_blank"></a>
                </td>
                <td class="stm_lms_manage_post_type__actions">

                    <a v-bind:href="post.edit_link" target="_blank" class="stm_lms_manage_post_type__btn edit">
						<?php esc_html_e('Edit', 'masterstudy'); ?>
                    </a>

                    <div v-if="post.status != 'publish'"
                         @click.prevent="changeStatus(index, post.id, 'publish')"
                         class="stm_lms_manage_post_type__btn publish">
						<?php esc_html_e('Publish', 'masterstudy'); ?>
                    </div>

                    <div v-else class="stm_lms_manage_post_type__actions_set">
                        <div class="stm_lms_manage_post_type__btn draft"
                             @click.prevent="changeStatus(index, post.id, 'draft')">
							<?php esc_html_e('Draft', 'masterstudy'); ?>
                        </div>
                        <div class="stm_lms_manage_post_type__btn trash"
                             @click.prevent="changeStatus(index, post.id, 'trash')">
							<?php esc_html_e('Trash', 'masterstudy'); ?>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="stm_lms_manage_post_type__filter">
            <div class="pagination" v-if="total > 1">
                <a href="#"
                   v-for="page in pages"
                   v-on:click.prevent="switchPage(page)"
                   v-bind:class="{'active' : page == current_page}">{{page}}</a>
            </div>

            <div class="filter">
                <select v-model="filter" v-on:change="switchStatus()">
                    <option value=""><?php esc_html_e('All', 'masterstudy'); ?></option>
                    <option value="publish"><?php esc_html_e('Published', 'masterstudy'); ?></option>
                    <option value="draft"><?php esc_html_e('Draft', 'masterstudy'); ?></option>
                    <option value="trash"><?php esc_html_e('Trash', 'masterstudy'); ?></option>
                </select>
            </div>
        </div>


    </div>
</div>