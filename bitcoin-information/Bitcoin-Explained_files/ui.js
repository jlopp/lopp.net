// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt AGPL-3.0
// script for wayback-ui.html
function update_aoheader() {
  // hide donation banner if "don't show" cookie is set. server side has
  // this control, but it won't take effect until cache expires.
  try {
    if ($.cookie('donation') === 'x') {
      $('#donate_banner').css('display', 'none');
    }
  } catch (err) {}
}
// @license-end
