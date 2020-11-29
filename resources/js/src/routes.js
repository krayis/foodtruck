import React, {Component} from 'react';
import {Route, Switch} from 'react-router-dom';
import SearchPage from "./containers/SearchPage";
import OrderPage from "./containers/OrderPage";
import CheckoutPage from "./containers/CheckoutPage";

class Routes extends Component {
    render() {
        return (
            <Switch>
                <Route path="/checkout/:id" component={CheckoutPage} />
                <Route path="/store/:id" component={OrderPage} />
                <Route path="/" component={SearchPage} />
            </Switch>
        )
    }
}

export default Routes;
