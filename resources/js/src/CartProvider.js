import React, {Component} from 'react';
import CartContext from './CartContext';

class CartProvider extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cart: [],
            count: 0,
            showCart: false,
            truck: {},
            event: {},
        };
    }

    render() {
        return (
            <CartContext.Provider
                value={{
                    truck: this.state.truck,
                    event: this.state.event,
                    cart: this.state.cart,
                    showCart: this.state.showCart,
                    addToCart: (obj) => {
                        this.setState({
                            cart: [...this.state.cart, obj.item],
                            truck: obj.truck,
                            event: obj.event,
                        })
                    },
                    updateTruck: (obj) => {
                        this.setState({
                            truck: obj,
                        })
                    },
                    openCart: () => {
                        this.setState({
                            showCart: true,
                        })
                    },
                    closeCart: (e) => {
                        this.setState({
                            showCart: false,
                        })
                    },
                    removeItem: (id) => {
                        this.setState({
                            cart: [...this.state.cart.filter(i => i._id !== id)]
                        })
                    }
                }}
            >
                {this.props.children}
            </CartContext.Provider>
        );
    }
}

export default CartProvider;
