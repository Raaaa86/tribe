<?php
if ( ! defined( 'myCRED_VERSION' ) ) return;

if ( ! class_exists( 'Esportz_Badge' ) ) {
    class Esportz_Badge {

        public $badge_options = array();

        public function __construct() {
            $this->badge_options = get_option( 'esportz_badges' );
        }

        /**
         * Returns badge settings by it's title and trigger
         *
         * @param string $badge_title
         * @param int $user_badge_trigger
         *
         * @return array | null
         */
        public function get_badge_by_trigger( $badge_title, $user_badge_trigger ) {
            $badge_levels = $this->badge_options[$badge_title];
            $result = null;

            foreach ( $badge_levels as $badge_level ) {
                $trigger = $badge_level['trigger'];

                if ( $user_badge_trigger === $trigger ) {
                    $result = $badge_level;
                    break;
                } else if ( $user_badge_trigger > $trigger ) {
                    $result = $badge_level;
                } else {
                    break;
                }

            }

            return $result;
        }

        /**
         * Returns all user badges state.
         *
         * @param $user_id
         * @return array User's badges state
         */
        public function get_user_badges( $user_id ) {
            return get_user_meta( $user_id, 'esportz_badges', true );
        }

        /**
         * Returns user's badge state.
         *
         * @param $user_id
         * @param $user_badge_title
         * @return int User's badge level
         */
        public function get_user_badge_level( $user_id, $user_badge_title ) {
            $badges = get_user_meta( $user_id, 'esportz_badges', true );
            if(isset($badges[$user_badge_title])) {
                return $badges[$user_badge_title];
            }

            return null;
        }

        /**
         * Returns current badge level data by user's badge state+
         *
         * @param int $user_id
         * @param string $user_badge_title Badge Title in user options (e.g. daily_logins, comments)
         *
         * @return array | null
         */
        public function get_current_user_badge( $user_id, $user_badge_title ) {
            $current_level = get_user_meta( $user_id, 'esportz_badges', true )[$user_badge_title];
            $result = null;

            if ( $user_badge_title === 'profile_fill' ) {
                if ( $current_level === 1 ) {
                    return $this->badge_options[$user_badge_title];
                } else {
                    return null;
                }
            }

            if ( ! isset( $this->badge_options[$user_badge_title] ) ) return $result;

            foreach ( $this->badge_options[$user_badge_title] as $badge_option ) {

                $trigger = $badge_option['trigger'];

                if ( $current_level === $trigger ) {
                    $result = $badge_option;
                    break;
                } else if ( $current_level > $trigger ) {
                    $result = $badge_option;
                } else {
                    break;
                }

            }

            return $result;

        }

        /**
         * Returns user's badges markup+
         *
         * @param $user_id
         * @param null $count
         * @return void
         */
        public function get_user_badges_markup( $user_id,  $count = null )
        {
            $user_badges = $this->get_user_badges($user_id);
            $returned_badges = 0;

            if (is_array($user_badges)){
                foreach ($user_badges as $user_badge_title => $user_badge_value) {

                    $badge = $this->get_current_user_badge($user_id, $user_badge_title);

                    if ($badge) {
                        $returned_badges++;
                        if ( $count !== null && $returned_badges > $count ) break;

                        $badge_img = $badge['img'];
                        $badge_desc = $badge['desc'];
                        ?>

                        <div class="item">
                            <div>
                                <img class="tltip" title="<?php echo esc_html($badge_desc); ?>"
                                     src="<?php echo esc_url($badge_img); ?>"
                                     alt="<?php echo esc_attr($badge_desc); ?>">
                            </div>
                        </div>

                        <?php
                    }

                }
            }else{
               echo '<div class="noAchv">'.esc_html__('No achievements yet!', 'esportz').'</div>';
            }

        }

        /**
         * Returns account age from sign up until today.
         *
         * @param $user_id
         * @return int months
         */
        public function get_account_age( $user_id ) {
            $user_data = get_userdata( $user_id );
            $registered = $user_data->user_registered;

            $reg_date = date_create( $registered );
            $today = date_create( date( 'Y-m-d' ) );
            $interval = date_diff( $reg_date, $today );

            $age_months = $interval->format( '%m' );

            return $age_months;
        }

        /**
         * Creates basic structure for working with user badges.
         * Used on sign up.
         *
         * @param $user_id
         * @return bool
         */
        public function set_basic_user_settings( $user_id ) {
            $earned = update_user_meta( $user_id, 'esportz_badges', array(
                'profile_fill' => 0,
                'daily_logins' => 0,
                'comments' => 0,
                'social_sharing' => 0,
                'reached_levels' => 1,
                'account_age' => 0,
                'won_tournaments' => 0,
            ) );

            return $earned ? true : false;
        }

        /**
         * Sets some user's badge value by its title
         *
         * @param int $user_id
         * @param string $user_badge_title Badge Title in user options (e.g. daily_logins, comments)
         * @param int $value Badge value
         *
         * @return bool
         */
        public function set_user_badge_value( $user_id, $user_badge_title, $value ) {
            $user_badges = get_user_meta( $user_id, 'esportz_badges', true );
            if(isset($user_badge_title) && !empty($user_badge_title))
            $user_badges[$user_badge_title] = $value;
            $user_badges_upd = update_user_meta( $user_id, 'esportz_badges', $user_badges );

            return $user_badges_upd ? true : false;
        }

        /**
         * Incrementing or decrementing user's badge value by its title
         *
         * @param int $user_id
         * @param string $user_badge_title Badge Title in user options (e.g. daily_logins, comments)
         * @param string $action Changing action ('inc' or 'dec')
         *
         * @return int | bool New badge's value or false
         */
        public function update_user_badge_level( $user_id, $user_badge_title, $action ) {
            $user_badges = get_user_meta( $user_id, 'esportz_badges', true );

            if ( $action === 'inc' ) {
                $user_badges[$user_badge_title] = (int) $user_badges[$user_badge_title] + 1;
            } else if ( $action === 'dec' ) {
                $user_badges[$user_badge_title] = (int) $user_badges[$user_badge_title] - 1;
            }

            $meta = update_user_meta( $user_id, 'esportz_badges', $user_badges );
            return $meta ? $user_badges[$user_badge_title] : false;
        }

        /**
         * Detects if user has reached a new level of badge and logs in in mycred
         *
         * @param int $user_id
         * @param string $user_badge_title Badge Title in user options (e.g. daily_logins, comments)
         * @param int $old_value Level of badge before value changing
         * @param int $new_value Level of badge after value changing
         *
         * @return bool
         */
        public function is_new_badge( $user_id, $user_badge_title, $old_value, $new_value ) {

            if(!class_exists('myCRED_Core')){
                return false;
            }

            $old_trigger = $this->get_badge_by_trigger( $user_badge_title, $old_value )['trigger'];
            $new_trigger = $this->get_badge_by_trigger( $user_badge_title, $new_value )['trigger'];

            if ( $old_trigger < $new_trigger ) {
                return mycred_add( 'new_badge', $user_id, 10, 'Reward for new badge', null, null, 'mycred_exp' );
            }

            return false;
        }

    }
}
