import React, { Component } from 'react';
import SquarePaymentForm from './SquarePaymentForm';

class Square extends Component {
    constructor(props){
        super(props)
        this.state = {
            loaded: false
        }
    }

    componentWillMount(){
        const that = this;
        let sqPaymentScript = document.createElement('script');
        sqPaymentScript.src = "https://js.squareup.com/v2/paymentform";
        sqPaymentScript.type = "text/javascript"
        sqPaymentScript.async = false;
        sqPaymentScript.onload = ()=>{that.setState({
            loaded: true
        })};
        document.getElementsByTagName("head")[0].appendChild(sqPaymentScript);
    }

    render() {
        return (
            this.state.loaded &&
            <SquarePaymentForm
                paymentForm={ window.SqPaymentForm }
            />
        );
    }
}

export default Square;
