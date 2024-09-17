import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Button from 'flarum/common/components/Button';
import type { SaveSubmitEvent } from 'flarum/admin/components/AdminPage';
import type { AlertIdentifier } from 'flarum/common/states/AlertManagerState';
import type Mithril from 'mithril';

import IndexPage from './pages/IndexPage';
import OtherPage from './pages/OtherPage';

export default class SettingsPage extends ExtensionPage {
  translationPrefix = 'foskym-wechat-official.admin.pages.';
  pages: any = {
    index: IndexPage,
    other: OtherPage,
  };
  icons: any = {
    index: 'home',
    other: 'cog',
  };

  content() {
    const page = m.route.param().page || 'index';

    return (
      <div className="ExtensionPage-settings WechatOfficial">
        <div className={'WechatOfficial-menu'}>
          <div className={'container'}>{this.menuButtons(page)}</div>
        </div>

        <div className="container WechatOfficial-container">{this.pageContent(page)}</div>
      </div>
    );
  }

  menuButtons(page: string) {
    return Object.keys(this.pages).map((key) =>
      Button.component(
        {
          className: `Button ${page === key ? 'item-selected' : ''}`,
          onclick: () =>
            m.route.set(
              app.route('extension', {
                id: 'foskym-wechat-official',
                page: key,
              })
            ),
          icon: this.iconForPage(key),
        },
        app.translator.trans(this.translationPrefix + key)
      )
    );
  }

  iconForPage(page: string) {
    return `fas fa-${this.icons[page]}` || '';
  }

  pageContent(page: string) {
    const PageComponent = this.pages[page];
    return PageComponent ? <PageComponent /> : null;
  }
}
