import app from 'flarum/admin/app';
import Page from 'flarum/common/components/Page';
import Button from 'flarum/common/components/Button';
import type Mithril from 'mithril';
import extractText from 'flarum/common/utils/extractText';
import icon from 'flarum/common/helpers/icon';

export default class OtherPage extends Page {
  translationPrefix = 'foskym-wechat-official.admin.other.';

  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);
  }

  view() {
    return (
      <div class={'WechatOfficial-ListPage'}>
        <Button className={'Button Button--danger'} onclick={this.deleteQRCode.bind(this)}>
          {icon('fas fa-trash')}
          {app.translator.trans('foskym-wechat-official.admin.other.delete_qrcode_cache')}
        </Button>

        <Button className={'Button Button--danger'} onclick={this.deleteWechatLink.bind(this)}>
          {icon('fas fa-trash')}
          {app.translator.trans('foskym-wechat-official.admin.other.delete_wechat_link')}
        </Button>
      </div>
    );
  }

  deleteQRCode() {
    window.confirm(extractText(app.translator.trans('foskym-wechat-official.admin.other.delete_qrcode_cache_confirm'))) &&
      app
        .request({
          method: 'DELETE',
          url: app.forum.attribute('apiUrl') + '/wechat-official/qrcode',
        })
        .then(() => {
          app.alerts.show({ type: 'success' }, app.translator.trans('foskym-wechat-official.admin.other.delete_qrcode_cache_success'));
          m.redraw();
        });
  }

  deleteWechatLink() {
    window.confirm(extractText(app.translator.trans('foskym-wechat-official.admin.other.delete_wechat_link_confirm'))) &&
    app
      .request({
        method: 'DELETE',
        url: app.forum.attribute('apiUrl') + '/wechat-official/qrcode',
      })
      .then(() => {
        app.alerts.show({ type: 'success' }, app.translator.trans('foskym-wechat-official.admin.other.delete_wechat_link_success'));
        m.redraw();
      });
}
}
