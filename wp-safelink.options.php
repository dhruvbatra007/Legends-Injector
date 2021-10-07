<?php
/*
	@package : themeson.com
	Author : Themeson
	Don't touch baby!
 */
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<style>
	ul.wpsafmenu {
		background: #fff;
		padding: 0 10px;
		border-bottom: 1px solid #d1d2d3;
	}

	ul.wpsafmenu li {
		list-style: none;
		display: inline-block;
		padding-top: 8px;
		margin: 0 5px 0 0;
	}

	ul.wpsafmenu li span {
		font-size: 14px;
		padding: 10px 15px;
		text-decoration: none;
		display: block;
		outline: 0;
		cursor: pointer;
		-webkit-border-radius: 12px;
		-moz-border-radius: 12px;
		border-radius: 5px 5px 0 0;
		margin-bottom: -1px
	}

	ul.wpsafmenu li span.actived {
		background: #f1f2f3;
		font-weight: bold;
		border: 1px solid #d1d2d3;
		border-bottom: 1px solid #f1f2f3;
	}

	ul.wpsafmenu li a:active {
		outline: none;
	}

	ul.wpsafmenu li #human {
		position: relative;
		padding-top: 5px;
	}

	ul.wpsafmenu li strong {
		position: absolute;
		left: 0px;
		bottom: -2px;
		font-size: 10px;
		color: red;
	}

	a:active {
		outline: none;
	}

	#safe_lists a {
		text-decoration: none;
		color: #000;
	}

	#safe_lists td {
		position: relative;
	}

	a.elips {
		width: auto;
		max-width: 100%;
		position: absolute;
		left: 10px;
		right: 10px;
		top: 6px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

    .invalid-format {
        border: 1px solid red;
    }

    .valid-format {
        border: 1px solid green;
    }

    #wpsaf-message {
        color: red;
    }
</style>
<div class="wrap">
	<h2>WP Safelink</h2>
	<ul class="wpsafmenu">
		<li><span id="setting" class="actived">Settings</span></li>
	</ul>

	<form action="?page=wp-safelink-client" method="post">
		<div class="wp-pattern-example">
			<div id="setting" class="tabcon">
				<h3>WP Safelink Server Import</h3>
				<table class="form-table">
					<tbody>
                        <tr>
							<td valign="" width="200px"><b>Activate WP Safelink Client</b></td>
							<td>
								<input <?php if (empty($wpsaf_client->active) || $wpsaf_client->active == 1) echo 'checked'; ?> type="radio" name="wpsaf_client[active]" value="1" id="active1"><label for="active">Yes</label>
								<input <?php if (isset($wpsaf_client->active) && $wpsaf_client->active == 2) echo 'checked'; ?> type="radio" name="wpsaf_client[active]" value="2" id="active0"><label for="active">No</label>
							</td>
						</tr>

						<tr>
							<td width="200px"><strong>Permalink</strong></td>
							<td>
								<input type="radio" name="wpsaf_client[permalink]" <?php if (empty($wpsaf_client->permalink) || isset($wpsaf_client->permalink) && $wpsaf_client->permalink == 1) echo "checked"; ?> value="1" id="permalink1">
								<label for="permalink1"><code><?php _e(home_url()); ?>/</code><input style="text-align:center" value="<?php echo isset($wpsaf_client->permalink1) ? $wpsaf_client->permalink1 : 'go'; ?>" type="text" size="12" name="wpsaf_client[permalink1]" />
									<code>/safelink_code</code></label><br />
								<input type="radio" name="wpsaf_client[permalink]" <?php if (isset($wpsaf_client->permalink) && $wpsaf_client->permalink == 2) echo "checked"; ?> value="2" id="permalink2">
								<label for="permalink2"><code><?php _e(home_url()); ?>/?</code><input style="text-align:center" value="<?php echo isset($wpsaf_client->permalink2) ? $wpsaf_client->permalink2 : 'go'; ?>" type="text" size="12" name="wpsaf_client[permalink2]" />
									<code>=safelink_code</code></label><br />
								<input type="radio" name="wpsaf_client[permalink]" <?php if (isset($wpsaf_client->permalink) && $wpsaf_client->permalink == 3) echo "checked"; ?> value="3" id="permalink3">
								<label for="permalink3"><code><?php _e(home_url()); ?>/?safelink_code</code></label>
							</td>
						</tr>

						<tr>
							<td width="200px"><strong>Paste your code from WP Safelink Server</strong></td>
                            <td><textarea rows="10" id="code_integrator" name="code_integrator" class="large-text code" <?php echo (!empty($base64) ? 'readonly' : ''); ?>><?php 
                                    echo $base64;
                                ?></textarea>
                                <p id="wpsaf-message">You can find the code at your WP Safelink Domain > wp-admin > WP Safelink > Settings > WP Safelink Client Integrator</p>
                            </td>
						</tr>
					</tbody>
				</table>

				<p style="margin-top: 20px;">
					<input id="save" <?php echo (empty($base64) ? 'disabled' : ''); ?> name="save" type="submit" class="button button-primary button-large" value="Save" />&nbsp;
					<input id="reset" name="reset" type="submit" class="button button-large" value="Reset Settings" />
				</p>

			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
jQuery(function($) {
    $('#code_integrator').keyup(function() {
        var str = $(this).val();
        if(str == "") {
			$('#save').attr('disabled', 'disabled');
            $(this).removeClass('invalid-format');

            $('#wpsaf-message').text('You can find the code at your WP Safelink Domain > wp-admin > WP Safelink > Settings > WP Safelink Client Integrator');
        } else if(str != "" && isBase64(str)) {
            var data = atob(str);

            if(isJson(data)) {
                data = JSON.parse(data);

                if(data.permalink1) {
                    wpSafValidFormat();
                } else {
                    wpSafInvalidFormat();   
                }
            } else {
                wpSafInvalidFormat();
            }
        } else {
            wpSafInvalidFormat();
        }
    });

    $('#reset').click(function(){
        var e = confirm( 'Are you sure to reset the code integrator?' );
        return e?void 0:!1
    })

    function wpSafValidFormat() {
        $('#save').removeAttr('disabled');
        $('#code_integrator').removeClass('invalid-format').addClass('valid-format');

        $('#wpsaf-message').text('');
    }

    function wpSafInvalidFormat() {
        $('#save').attr('disabled', 'disabled');
        $('#code_integrator').addClass('invalid-format').removeClass('valid-format');

        $('#wpsaf-message').text('Invalid WP Safelink Server Code');
    }
});

function isBase64(str) {
    if (str ==='' || str.trim() ===''){ return false; }
    try {
        return btoa(atob(str)) == str;
    } catch (err) {
        return false;
    }
}

function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
</script>