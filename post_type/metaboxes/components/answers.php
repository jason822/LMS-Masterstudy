<div>
	<div class="stm-lms-questions">

        <transition name="slide-fade">
            <div class="stm-lms-questions-single stm-lms-questions-single_choice"
                 v-if="choice == 'single_choice' && questions.length">

                <div class="stm-lms-questions-single_answer" v-for="(v,k) in questions">
                    <label class="stm_lms_radio" v-bind:class="{'active' : v.isTrue}">
                        <input type="radio" v-bind:name="choice + '_' + origin" v-model="correctAnswer" v-bind:value="v.text" @change="isAnswer()" />
                        <i></i>
                        <input type="text" v-model="questions[k]['text']" />

                        <textarea v-model="questions[k]['explain']"
                                  placeholder="<?php esc_html_e('Answer explanation (Will be shown in "Show Answers" section)', 'masterstudy-lms-learning-management-system') ?>"></textarea>
                    </label>
                    <div class="actions">
                        <i class="lnr lnr-trash" @click="deleteAnswer(k)"></i>
                    </div>
                </div>

            </div>
        </transition>

        <transition name="slide-fade">
            <div class="stm-lms-questions-single stm-lms-questions-multi_choice"
                 v-if="choice == 'multi_choice' && questions.length">

                <div class="stm-lms-questions-single_answer" v-for="(v,k) in questions">
                    <label class="stm_lms_checkbox" v-bind:class="{'active' : v.isTrue}">
                        <input type="checkbox" v-bind:name="choice" v-model="correctAnswers[v.text]" v-bind:value="v.text" @change="isAnswers()" />
                        <i class="fa fa-check"></i>
                        <input type="text" v-model="questions[k]['text']" />

                        <textarea v-model="questions[k]['explain']"
                                  placeholder="<?php esc_html_e('Answer explanation (Will be shown in "Show Answers" section)', 'masterstudy-lms-learning-management-system') ?>"></textarea>

                    </label>
                    <div class="actions">
                        <i class="lnr lnr-trash" @click="deleteAnswer(k)"></i>
                    </div>
                </div>

            </div>
        </transition>

        <transition name="slide-fade">
            <div class="stm-lms-questions-single stm-lms-questions-true_false"
                 v-if="choice == 'true_false' && questions.length">

                <div class="stm-lms-questions-single_answer" v-for="(v,k) in questions">
                    <label class="stm_lms_radio" v-bind:class="{'active' : v.isTrue}">
                        <input type="radio" v-bind:name="choice" v-model="correctAnswer" v-bind:value="v.text" @change="isAnswer()" />
                        <i></i>
                        <span>{{ v.text }}</span>
                    </label>
                </div>

            </div>
        </transition>

        <div class="stm_lms_answers_container" v-if="choice !== 'true_false'">
            <div class="stm_lms_answers_container__input">
		        <input type="text"
                       v-model="new_answer"
                       v-bind:class="{'shake-it' : isEmpty}"
                       @keydown.enter.prevent="addAnswer()"
                       placeholder="<?php esc_html_e('Enter new Answer', 'masterstudy-lms-learning-management-system'); ?>"/>
            </div>
            <div class="stm_lms_answers_container__submit">
                <a class="button" @click="addAnswer()"><?php esc_html_e('Add Answer') ?></a>
            </div>
        </div>

	</div>
</div>