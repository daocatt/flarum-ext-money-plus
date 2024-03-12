import app from 'flarum/forum/app';
import { extend } from 'flarum/extend';
import UserPage from 'flarum/forum/components/UserPage';
import UserCard from 'flarum/components/UserCard';
import UserControls from 'flarum/utils/UserControls';
import Button from 'flarum/components/Button';
import UserMoneyModal from './components/UserMoneyModal';
import Model from 'flarum/Model';
import User from 'flarum/models/User';

import LinkButton from 'flarum/common/components/LinkButton';
import MoneyHistoryPage from './components/MoneyHistoryPage';
import UserMoneyHistory from "./models/UserMoneyHistory";

app.initializers.add('gtdxyz-money-plus', () => {
  User.prototype.canEditMoney = Model.attribute('canEditMoney');
  app.store.models.userMoneyHistory = UserMoneyHistory;
  app.routes.userMoneyHistory = {
    path: '/u/:username/money/history',
    component: MoneyHistoryPage,
  };

  extend(UserCard.prototype, 'infoItems', function (items) {
    const moneyName = app.forum.attribute('gtdxyz-money-plus.moneyname') || 'MO';
    const moneyCur = this.attrs.user.data.attributes['money'];

    if (app.forum.attribute('gtdxyz-money-plus.noshowzero') == 1) {
      if (this.attrs.user.data.attributes.money !== 0) {
        items.add('money',
          <span>{moneyCur} {moneyName}</span>
        );
      }
    } else {
      items.add('money',
        <span>{moneyCur} {moneyName}</span>
      );
    }
  });

  extend(UserControls, 'moderationControls', (items, user) => {
    if (user.canEditMoney()) {
      items.add('money', Button.component({
        icon: 'fas fa-money-bill',
        onclick: () => app.modal.show(UserMoneyModal, {user})
      }, app.translator.trans('gtdxyz-money-plus.forum.user_controls.money_button')+app.forum.attribute('gtdxyz-money-plus.moneyname')));
    }
  });

  extend(UserPage.prototype, 'navItems', function (items) {
    if (!app.session.user) {
      return;
    }


    items.add(
      'userMoneyHistory',
      LinkButton.component({
        href: app.route('userMoneyHistory', {
          username: app.session.user.username(),
        }),
        icon: 'fas fa-wallet',
      }, app.forum.attribute('gtdxyz-money-plus.moneyname')+app.translator.trans('gtdxyz-money-plus.history.forum.nav')
      ),
      10
    );
  });

});
