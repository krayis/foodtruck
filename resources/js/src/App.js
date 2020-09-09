import React, {Component} from 'react';
import Routes from './routes';
import {  BrowserRouter as Router } from 'react-router-dom';
import Header from './containers/Header';
import CartProvider from './CartProvider';
import Cart from './containers/Cart';

class App extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cartCount: 0,
        };

        this.addToCart = this.addToCart.bind(this)
    }

    addToCart() {
        this.setState({ cartCount: this.state.cartCount + 1 })
    }
    render() {
        return (
            <CartProvider>
                <Router>
                    <Header />
                    <Routes />
                    <Cart />
                </Router>
            </CartProvider>
        );
    }
}

export default App;
