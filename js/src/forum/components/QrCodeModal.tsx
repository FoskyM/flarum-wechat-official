import app from 'flarum/forum/app';
import { extend, override } from 'flarum/common/extend';
import Modal from 'flarum/common/components/Modal';
import Alert from 'flarum/common/components/Alert';
import ItemList from 'flarum/common/utils/ItemList';
import Button from 'flarum/common/components/Button';
import LogInModal from 'flarum/forum/components/LogInModal';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import type Mithril from 'mithril';

export default class QrCodeModal extends Modal {
  qrCodeUrl: string | null = null;
  qrCodeId: number = 0;
  qrCodeType: string = 'bind';
  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);
    // @ts-ignore
    this.qrCodeType = this.attrs.type || 'bind';
    this.loading = true;
    app
      .request({
        method: 'GET',
        url: app.forum.attribute('apiUrl') + '/wechat-official/qrcode',
      })
      .then((response: any) => {
        this.qrCodeUrl = response.url;
        this.qrCodeId = response.id;
        this.loading = false;
        setInterval(this.checkScanStatus.bind(this), 5000);
        m.redraw();
      });
  }

  className() {
    return 'Modal--small';
  }

  title() {
    return app.translator.trans(`foskym-wechat-official.forum.scan.${this.qrCodeType}.title`);
  }

  fields() {
    const items = new ItemList();

    items.add(
      'qrcode',
      <div className="Form-group">{this.loading ? <LoadingIndicator /> : <img src={this.qrCodeUrl} alt="QR Code" style="width: 100%" />}</div>
    );

    return items;
  }

  body() {
    return [<div className="Form Form--centered">{this.fields().toArray()}</div>];
  }

  footer() {
    return app.translator.trans(`foskym-wechat-official.forum.scan.${this.qrCodeType}.description`);
  }

  content() {
    return [<div className="Modal-body">{this.body()}</div>, <div className="Modal-footer">{this.footer()}</div>];
  }

  onsubmit(e: SubmitEvent) {
    e.preventDefault();
    // this.loading = true;
  }

  checkScanStatus() {
    app
      .request({
        method: 'GET',
        url: app.forum.attribute('apiUrl') + '/wechat-official/qrcode/' + this.qrCodeId,
        body: {},
      })
      .then((response: any) => {
        if (response.status === 'scaned') {
          app.modal.close();
          window.location.reload();
        }
      });
  }
}
