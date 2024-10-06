<?php
/**
 * Plugin Name: Rocket55 Super Debug Tool
 * Description: A plugin for displaying extensive debug information for posts, pages, archives, and taxonomies in the frontend.
 * Version: 1.0.5
 * Author: Pierre Balian
 * Author URI: https://www.rocket55.com
 * License: GPL2
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class R55_Super_Debug_Tool {
    public function __construct() {
        add_action( 'wp_footer', array( $this, 'display_debug_information' ) );
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_button' ), 100 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Displays the debug information modal when the debug tool is activated.
     */
    public function display_debug_information() {
        if ( is_single() && defined( 'WP_DEBUG' ) && WP_DEBUG && isset( $_GET['debugtool'] ) ) {
            global $post, $wp_query;

            $post_id = $post->ID;

            // Collect debug information
            $all_meta            = get_post_meta( $post_id );
            $taxonomies          = get_object_taxonomies( $post->post_type, 'objects' );
            $terms               = array();
            foreach ( $taxonomies as $taxonomy ) {
                $taxonomy_terms = get_the_terms( $post_id, $taxonomy->name );
                if ( ! empty( $taxonomy_terms ) && ! is_wp_error( $taxonomy_terms ) ) {
                    $terms[ $taxonomy->name ] = wp_list_pluck( $taxonomy_terms, 'name' );
                }
            }
            $post_object         = $post;
            $attached_media      = get_attached_media( '', $post_id );
            $parent_post         = $post->post_parent ? get_post( $post->post_parent ) : null;
            $child_posts         = get_children( array( 'post_parent' => $post_id ) );
            $post_type           = get_post_type( $post_id );
            $template            = get_page_template_slug( $post_id );
            $author_data         = get_userdata( $post->post_author );
            $wp_query_info       = array(
                'is_main_query' => $wp_query->is_main_query(),
                'query_vars'    => $wp_query->query_vars,
            );
            $comments            = get_comments( array( 'post_id' => $post_id ) );
            $rewrite_rules       = get_option( 'rewrite_rules' );
            $enqueued_scripts    = wp_scripts();
            $enqueued_styles     = wp_styles();

            // Get ACF fields
            if ( function_exists( 'get_field_objects' ) ) {
                $acf_fields             = get_field_objects( $post_id );
                $acf_field_names_values = array();
                if ( $acf_fields ) {
                    foreach ( $acf_fields as $field ) {
                        $acf_field_names_values[] = array(
                            'label' => $field['label'],
                            'name'  => $field['name'],
                            'value' => $field['value'],
                        );
                    }
                } else {
                    $acf_field_names_values = 'No ACF fields found for this post.';
                }
            } else {
                $acf_fields             = 'ACF is not active.';
                $acf_field_names_values = 'ACF is not active.';
            }

            // Get custom post types
            $args              = array(
                'public'   => true,
                '_builtin' => false,
            );
            $custom_post_types = get_post_types( $args, 'objects' );

            // Get custom taxonomies
            $custom_taxonomies = get_taxonomies( array( '_builtin' => false ), 'objects' );

            // Enqueue Highlight.js for syntax highlighting (use a CDN)
            wp_enqueue_style( 'highlightjs-css', 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/github.min.css' );
            wp_enqueue_script( 'highlightjs-js', 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js', array(), null, true );

            // Output the modal structure
            ?>
            <div class="modal fade" id="metaKeysModal" tabindex="-1" aria-labelledby="metaKeysModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-xl">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="metaKeysModalLabel">Debug Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="d-inline-flex flex-wrap gap-2 mb-3">
                      <!-- Navigation Pills -->
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('metaKeysSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Meta Keys</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('taxonomiesSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Taxonomies & Terms</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('postObjectSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Post Object</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('mediaSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Attached Media</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('parentChildSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Parent & Child Posts</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('postTypeSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Post Type & Template</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('authorSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Author Info</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('queryInfoSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Query Info</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('commentsSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Comments</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('rewriteRulesSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Rewrite Rules</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('scriptsStylesSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Enqueued Scripts & Styles</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('acfFieldsSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">ACF Fields</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('customPostTypesSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Custom Post Types</a>
                      <a href="#" class="btn btn-primary debug-pill" onclick="showSection('customTaxonomiesSection'); return false;" style="padding: 0.3rem 0.5rem; font-size: 0.875rem;">Custom Taxonomies</a>
                    </div>

                    <!-- Sections -->
                    <div id="metaKeysSection" class="debug-section">
                      <h5>Post Meta:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $all_meta ); ?></code></pre>
                    </div>

                    <div id="taxonomiesSection" class="debug-section" style="display:none;">
                      <h5>Taxonomies and Terms:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $terms ); ?></code></pre>
                    </div>

                    <div id="postObjectSection" class="debug-section" style="display:none;">
                      <h5>Post Object:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $post_object ); ?></code></pre>
                    </div>

                    <div id="mediaSection" class="debug-section" style="display:none;">
                      <h5>Attached Media:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $attached_media ); ?></code></pre>
                    </div>

                    <div id="parentChildSection" class="debug-section" style="display:none;">
                      <h5>Parent Post:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $parent_post ); ?></code></pre>
                      <h5>Child Posts:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $child_posts ); ?></code></pre>
                    </div>

                    <div id="postTypeSection" class="debug-section" style="display:none;">
                      <h5>Post Type:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $post_type ); ?></code></pre>
                      <h5>Template:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $template ); ?></code></pre>
                    </div>

                    <div id="authorSection" class="debug-section" style="display:none;">
                      <h5>Author Information:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $author_data ); ?></code></pre>
                    </div>

                    <div id="queryInfoSection" class="debug-section" style="display:none;">
                      <h5>WP Query Information:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $wp_query_info ); ?></code></pre>
                    </div>

                    <div id="commentsSection" class="debug-section" style="display:none;">
                      <h5>Comments:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $comments ); ?></code></pre>
                    </div>

                    <div id="rewriteRulesSection" class="debug-section" style="display:none;">
                      <h5>Rewrite Rules:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $rewrite_rules ); ?></code></pre>
                    </div>

                    <div id="scriptsStylesSection" class="debug-section" style="display:none;">
                      <h5>Enqueued Scripts:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $enqueued_scripts->queue ); ?></code></pre>
                      <h5>Enqueued Styles:</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $enqueued_styles->queue ); ?></code></pre>
                    </div>

                    <div id="acfFieldsSection" class="debug-section" style="display:none;">
                      <h5>ACF Fields:</h5>
                      <div class="mb-3">
                        <ul>
                          <?php
                          if ( is_array( $acf_field_names_values ) ) {
                              foreach ( $acf_field_names_values as $field ) {
                                  $value = is_array( $field['value'] ) ? json_encode( $field['value'] ) : $field['value'];
                                  echo '<li><strong>' . esc_html( $field['label'] ) . ' (' . esc_html( $field['name'] ) . '):</strong> ' . esc_html( $value ) . '</li>';
                              }
                          } else {
                              echo '<li>' . esc_html( $acf_field_names_values ) . '</li>';
                          }
                          ?>
                        </ul>
                      </div>
                      <h5>ACF Fields (Detailed):</h5>
                      <pre><code class="json"><?php echo $this->format_debug_data( $acf_fields ); ?></code></pre>
                    </div>

                    <div id="customPostTypesSection" class="debug-section" style="display:none;">
                      <h5>Custom Post Types:</h5>
                      <ul>
                        <?php
                        if ( ! empty( $custom_post_types ) ) {
                            foreach ( $custom_post_types as $post_type_obj ) {
                                echo '<li><strong>' . esc_html( $post_type_obj->label ) . '</strong> (' . esc_html( $post_type_obj->name ) . ')</li>';
                            }
                        } else {
                            echo '<li>No custom post types found.</li>';
                        }
                        ?>
                      </ul>
                    </div>

                    <div id="customTaxonomiesSection" class="debug-section" style="display:none;">
                      <h5>Custom Taxonomies:</h5>
                      <ul>
                        <?php
                        if ( ! empty( $custom_taxonomies ) ) {
                            foreach ( $custom_taxonomies as $taxonomy ) {
                                echo '<li><strong>' . esc_html( $taxonomy->label ) . '</strong> (' . esc_html( $taxonomy->name ) . ')</li>';
                            }
                        } else {
                            echo '<li>No custom taxonomies found.</li>';
                        }
                        ?>
                      </ul>
                    </div>

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Custom CSS and JavaScript -->
            <style>
                .hljs-attr { color: #d35400; } /* Keys - dark orange */
                .hljs-string { color: #007bff; } /* Values - blue */
            </style>
            <script>
              document.addEventListener("DOMContentLoaded", function() {
                var metaKeysModal = new bootstrap.Modal(document.getElementById("metaKeysModal"));
                metaKeysModal.show();
                // Show the first section by default
                showSection('metaKeysSection');
                hljs.highlightAll();
              });

              function showSection(sectionId) {
                var sections = document.querySelectorAll(".debug-section");
                sections.forEach(function(section) {
                  section.style.display = "none";
                });
                var targetSection = document.getElementById(sectionId);
                if (targetSection) {
                  targetSection.style.display = "block";
                  hljs.highlightAll();
                }
              }
            </script>
            <?php
        }
    }

    /**
     * Formats debug data into pretty-printed JSON.
     *
     * @param mixed $data The data to format.
     * @return string HTML-escaped JSON string.
     */
    private function format_debug_data( $data ) {
        return htmlspecialchars( json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), ENT_QUOTES, 'UTF-8' );
    }

    /**
     * Enqueues Bootstrap assets when the debug tool is activated and the setting is enabled.
     */
    public function enqueue_assets() {
        if ( isset( $_GET['debugtool'] ) && get_option( 'r55_debug_tool_load_bootstrap', 'yes' ) === 'yes' ) {
            // Enqueue Bootstrap assets
            wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' );
            wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), null, true );
        }
    }

    /**
     * Adds a settings page under the "Settings" menu.
     */
    public function add_settings_page() {
        add_options_page(
            'R55 Super Debug Tool',
            'R55 Super Debug Tool',
            'manage_options',
            'r55-debug-tool-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Renders the settings page content.
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>R55 Super Debug Tool</h1>
            <p>
                I created this as an internal tool to aid in debugging, but am offering it to the wider community as-is and free of support.
                Issues and PR's may be submitted via <a href="https://github.com/YourGitHubRepo" target="_blank">GitHub</a>.
            </p>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'r55_debug_tool_settings' );
                do_settings_sections( 'r55-debug-tool-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Registers settings and adds settings fields.
     */
    public function register_settings() {
        register_setting( 'r55_debug_tool_settings', 'r55_debug_tool_load_bootstrap' );
        register_setting( 'r55_debug_tool_settings', 'r55_enable_wp_debug' );
        register_setting( 'r55_debug_tool_settings', 'r55_enable_script_debug' );
        register_setting( 'r55_debug_tool_settings', 'r55_enable_display_errors' );

        add_settings_section(
            'r55_debug_tool_main_section',
            'Main Settings',
            null,
            'r55-debug-tool-settings'
        );

        add_settings_field(
            'r55_debug_tool_load_bootstrap',
            'Load Bootstrap Assets',
            array( $this, 'load_bootstrap_field_callback' ),
            'r55-debug-tool-settings',
            'r55_debug_tool_main_section'
        );

        add_settings_field(
            'r55_enable_wp_debug',
            'Enable WP Debug',
            array( $this, 'wp_debug_field_callback' ),
            'r55-debug-tool-settings',
            'r55_debug_tool_main_section'
        );

        add_settings_field(
            'r55_enable_script_debug',
            'Enable Script Debug',
            array( $this, 'script_debug_field_callback' ),
            'r55-debug-tool-settings',
            'r55_debug_tool_main_section'
        );

        add_settings_field(
            'r55_enable_display_errors',
            'Enable Display Errors',
            array( $this, 'display_errors_field_callback' ),
            'r55-debug-tool-settings',
            'r55_debug_tool_main_section'
        );
    }

    /**
     * Callback function for the "Enable WP Debug" setting field.
     */
    public function wp_debug_field_callback() {
        $option = get_option( 'r55_enable_wp_debug', 'no' );
        ?>
        <label>
            <input type="checkbox" name="r55_enable_wp_debug" value="yes" <?php checked( 'yes', $option ); ?>>
            Enable WP Debug (Note: This setting cannot override wp-config.php)
        </label>
        <?php
    }

    /**
     * Callback function for the "Enable Script Debug" setting field.
     */
    public function script_debug_field_callback() {
        $option = get_option( 'r55_enable_script_debug', 'no' );
        ?>
        <label>
            <input type="checkbox" name="r55_enable_script_debug" value="yes" <?php checked( 'yes', $option ); ?>>
            Enable Script Debug (Note: This setting cannot override wp-config.php)
        </label>
        <?php
    }

    /**
     * Callback function for the "Enable Display Errors" setting field.
     */
    public function display_errors_field_callback() {
        $option = get_option( 'r55_enable_display_errors', 'no' );
        ?>
        <label>
            <input type="checkbox" name="r55_enable_display_errors" value="yes" <?php checked( 'yes', $option ); ?>>
            Enable Display Errors (Note: This setting cannot override wp-config.php)
        </label>
        <?php
    }

    /**
     * Adds an admin bar button to activate the debug tool.
     *
     * @param WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance.
     */
    public function add_admin_bar_button( $wp_admin_bar ) {
        if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
            $current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $url_parts   = parse_url( $current_url );
            $scheme      = isset( $url_parts['scheme'] ) ? $url_parts['scheme'] : 'http';
            $host        = $url_parts['host'];
            $path        = isset( $url_parts['path'] ) ? $url_parts['path'] : '';
            $query       = array();
            if ( isset( $url_parts['query'] ) ) {
                parse_str( $url_parts['query'], $query );
            }
            $query['debugtool'] = '1';
            $new_query_string   = http_build_query( $query );
            $debug_url          = $scheme . '://' . $host . $path . '?' . $new_query_string;

            $args = array(
                'id'    => 'r55_debug_tool_toggle',
                'title' => 'Debug Tool',
                'href'  => esc_url( $debug_url ),
                'meta'  => array(
                    'class' => 'r55-debug-tool-toggle',
                ),
            );
            $wp_admin_bar->add_node( $args );
        }
    }
}
new R55_Super_Debug_Tool();