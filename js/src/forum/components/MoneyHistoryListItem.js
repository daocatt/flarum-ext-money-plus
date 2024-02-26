import Component from "flarum/Component";
import Link from "flarum/components/Link";
import avatar from "flarum/helpers/avatar";
import username from "flarum/helpers/username";

export default class TransferHistoryListItem extends Component {
  view() {
    const {userMoneyHistory} = this.attrs;
    const changeTime = userMoneyHistory.changeTime();
    const money = userMoneyHistory.money();
    const sourceDesc = userMoneyHistory.sourceDesc();
    const moneyID = userMoneyHistory.id();
    const moneyUser = userMoneyHistory.user();
    const createUser = userMoneyHistory.createUser();
    const balanceMoney = userMoneyHistory.balanceMoney();
    const lastMoney = userMoneyHistory.lastMoney();
    const moneyType = app.translator.trans(userMoneyHistory.type()==='D'?"gtdxyz-money-plus.history.forum.record.money-out":"gtdxyz-money-plus.history.forum.record.money-in");
    const moneyTypeStyle = userMoneyHistory.type()==='D'?"color:red":"color:green";

    return (
      <div className="transferHistoryContainer">
        <div className="history-item-type">
          <i className="fas fa-coins"></i>
          <span className="changeType" style={moneyTypeStyle}>{moneyType}</span>
          <span className="changeTime">{changeTime}</span>
        </div>

        <div className="history-item-source">
          <span className="moneyHistoryUser">{username(createUser)}</span>
          <span className="changeSource">
            {sourceDesc}
          </span>
          <span className="changeDetail">
            {app.translator.trans('gtdxyz-money-plus.history.forum.record.money-list-change')}:&nbsp;&nbsp;&nbsp;{money}
          </span>
          
        </div>
      </div>
    );
  }
}
