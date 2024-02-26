import Component from "flarum/Component";
import app from "flarum/app";
import LoadingIndicator from "flarum/components/LoadingIndicator";
import Button from "flarum/components/Button";

import MoneyHistoryListItem from "./MoneyHistoryListItem";

export default class TransferHistoryList extends Component {
  oninit(vnode) {
    super.oninit(vnode);
    this.loading = true;
    this.moreResults = false;
    this.userMoneyHistory = [];
    this.user = this.attrs.params.user;
    this.loadResults();
  }

  view() {
    let loading;

    if (this.loading) {
      loading = LoadingIndicator.component({ size: "large" });
    }
    if(app.session.user){
      return (    
        <div className="money-history-list">
          <div className="title">
            {app.translator.trans("gtdxyz-money-plus.history.forum.title")}
          </div>
          <ul className="ul-list">
            {this.userMoneyHistory.map((userMoneyHistory) => {
              return (
                <li key={userMoneyHistory.id} data-id={userMoneyHistory.id}>
                  {MoneyHistoryListItem.component({ userMoneyHistory })}
                </li>
              );
            })}
          </ul>

          {!this.loading && this.userMoneyHistory.length===0 && (
            <div>
              <div className="empty-list">{app.translator.trans("gtdxyz-money-plus.history.forum.list-empty")}</div>
            </div>
          )}

          {this.hasMoreResults() && (
            <div className="load-more">
              <Button className={'Button Button--primary'} disabled={this.loading} loading={this.loading} onclick={() => this.loadMore()}>
                {app.translator.trans('gtdxyz-money-plus.history.forum.money-list-load-more')}
              </Button>
            </div>
          )}
        </div>
      );
    }
  }

  loadMore() {
    this.loading = true;
    this.loadResults(this.userMoneyHistory.length);
  }

  parseResults(results) {
    this.moreResults = !!results.payload.links && !!results.payload.links.next;
    [].push.apply(this.userMoneyHistory, results);
    this.loading = false;
    m.redraw();

    return results;
  }

  hasMoreResults() {
    return this.moreResults;
  }

  loadResults(offset = 0) {
    let url = '/users/' + this.user.id() + '/money/history';
    return app.store
      .find(url, {
        filter: {
          user: this.user.id(),
        },
        page: {
          offset,
        },
      })
      .catch(() => {})
      .then(this.parseResults.bind(this));
  }
}
