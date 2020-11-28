import React, {Component} from 'react';
import {
    Link
} from "react-router-dom";
import axios from 'axios';
import queryString from 'query-string';
import CartContext from '../CartContext';
import {format, formatRelative, formatISO9075} from 'date-fns';
import Square from './Square';

function range(start, stop, step) {
    if (typeof stop == 'undefined') {
        stop = start;
        start = 0;
    }
    if (typeof step == 'undefined') {
        step = 1;
    }
    if ((step > 0 && start >= stop) || (step < 0 && start <= stop)) {
        return [];
    }
    const result = [];
    for (var i = start; step > 0 ? i < stop : i > stop; i += step) {
        result.push(i);
    }

    return result;
}

function calculateItemPrice(item) {
    let price = parseFloat(item.price);
    for(let i=0; i<item.modifiers.length; i++) {
        price += parseFloat(item.modifiers[i].price);
    }
    return price.toFixed(2);
}

function convertToDateTime(dateTime) {
    const dateTimeParts = dateTime.split(/[- :]/);
    dateTimeParts[1]--;
    return new Date(...dateTimeParts);

}

class CheckoutPage extends Component {
    constructor(props) {
        super(props);
        this.state = {
            tip: 0,
            loading: true,
            input: {}

        };
        this.fetchData();
        this.selectTime = this.selectTime.bind(this);
        this.selectEvent = this.selectEvent.bind(this);
        this.selectedEvent = this.selectedEvent.bind(this);
        this.createTimeOptions = this.createTimeOptions.bind(this);
        this.createDateOptions = this.createDateOptions.bind(this);
        this.handleTipChange = this.handleTipChange.bind(this);
        this.tipActive = this.tipActive.bind(this);
        this.getCurrentEvent = this.getCurrentEvent.bind(this);
        this.getLocation = this.getLocation.bind(this);
    }

    fetchData() {
        const params = queryString.parse(location.search);
        axios.get(`/api/checkout/${this.props.match.params.id}`, {params}).then(response => {
            this.context.updateTruck(response.data.truck);
            this.setState({
                ...response.data,
                loading: false,
                input: {
                    tip: response.data.order.tip,
                }
            })
        });
    }

    handleTipChange(e) {
        const target = e.target;
        const tip = target.value;
        const state = Object.assign({}, this.state);
        state.order.tip = Number(tip.replace(/[^0-9.-]+/g,"")).toFixed(2);
        if (target.type === 'radio' && target.value !== 'other') {
            this.setState({
                ...state,
                custom_tip: false,
            });
        } else if (target.type === 'radio' && target.value === 'other') {
            this.setState({
                ...state,
                custom_tip: true,
            });
        } else if (target.type === 'text') {
            this.setState({
                custom_tip: false,
                ...state,
            });
        }
        axios.post(`/api/checkout/${this.props.match.params.id}`,{
            _method: 'PATCH',
            tip: tip,
        });
    }

    createTimeOptions() {
        const event = this.getCurrentEvent();

        let dateTimeParts = event.start_date_time.split(/[- :]/);
        dateTimeParts[1]--;
        let startTime = new Date(...dateTimeParts);

        dateTimeParts = event.end_date_time.split(/[- :]/);
        dateTimeParts[1]--;
        const endTime = new Date(...dateTimeParts);

        dateTimeParts = event.now_date_time.split(/[- :]/);
        dateTimeParts[1]--;
        const nowTime = new Date(...dateTimeParts);

        if(nowTime.getTime() >= startTime.getTime()) {
            startTime = nowTime;
        }

        const minutes = format(startTime, 'm');
        const now = startTime.getTime() + ((30 - (minutes % 15)) * 60 * 1000);

        const timeRange = range(now, endTime.getTime(), 15 * 60 * 1000);

        const options = [];
        for (let i = 0; i<timeRange.length; i++) {
            const startTime = new Date(timeRange[i]);
            const endTime = new Date(timeRange[i] + (15 * 60 * 1000));
            options.push(<option value={formatISO9075(startTime)} >{format(startTime, 'h:mm a')} - {format(endTime, 'h:mm a')}</option>)
        }
        return options;
    }

    createDateOptions() {
        const events = this.state.events;
        const options = [];
        for (let i = 0; i<events.length; i++) {
            let dateTimeParts = events[i].start_date_time.split(/[- :]/);
            dateTimeParts[1]--;
            const dateTime = new Date(...dateTimeParts);
            dateTimeParts = events[i].now_date_time.split(/[- :]/);
            dateTimeParts[1]--;
            const nowTime = new Date(...dateTimeParts);

            if (format(dateTime, 'EEEE MMMM d') == format(nowTime, 'EEEE MMMM d') ) {
                options.push(<option key={`events-${i}`} value={events[i].id}>Today {format(dateTime, 'MMMM d')} at {events[i].location.name}</option>)
            } else {
                options.push(<option key={`events-${i}`} value={events[i].id}>{format(dateTime, 'EEEE MMMM d')} at {events[i].location.name}</option>)
            }
        }
        return options;
    }

    selectEvent(e) {
        let event = null;
        for (let i = 0; i<this.state.events.length; i++) {
            if (this.state.events[i].id == e.target.value) {
                event = this.state.events[i];
            }
        }
        axios.post(`/api/checkout/${this.props.match.params.id}`,{
            _method: 'PATCH',
            event_id: e.target.value,
        }).then((response) => {
            this.setState({
                ...response.data,
            });
        });
    }
    selectTime(e) {
        axios.post(`/api/checkout/${this.props.match.params.id}`,{
            _method: 'PATCH',
            pickup_at: e.target.value,
        }).then((response) => {
            this.setState({
                ...response.data,
            });
        });
    }

    selectedEvent() {
        let event = null;
        for (let i = 0; i<this.state.events.length; i++) {
            if (this.state.events[i].id == this.state.event.id) {
                event = this.state.events[i];
            }
        }
        return event;
    }

    getCurrentEvent() {
        let event = {};
        for (let i = 0; i<this.state.events.length; i++) {
            if (this.state.events[i].id == this.state.order.event_id) {
                event = this.state.events[i];
            }
        }
        console.log(event)
        return event;
    }

    getLocation() {
        const event = this.getCurrentEvent();
        const location = event.location;
        return location;
    }

    tipActive(tip) {
        return this.state.order.tip == tip;
    }

    calculateGrandTotal() {
        return this.state.order.sub_total + this.state.order.tax_total + this.state.order.tip;
    }

    render() {
        if (this.state.loading === true) {
            return (
                <div className="container--checkout">
                    Loading...
                </div>
            )
        }
        let dateTimeParts = this.state.event.end_date_time.split(/[- :]/);
        dateTimeParts[1]--;
        const endDateTime = new Date(...dateTimeParts);

        return (
            <React.Fragment>
                <div className="container--checkout">
                    <div className="checkout-summary-inner">
                        <div className="section">
                            <h3>Order from</h3>
                            <p>{this.state.truck.name}</p>
                        </div>
                        <hr/>
                        <div className="section">
                            <h3>Pick up details</h3>
                            <div className="section pickup-meta">
                                <div className="form-group">
                                    <label>Select a date and location</label>
                                    <select defaultValue={this.state.order.event_id} onChange={this.selectEvent}>
                                        {this.createDateOptions()}
                                    </select>
                                </div>
                                <div className="form-group">
                                    <label>Select time</label>
                                    <select onChange={this.selectTime}>
                                        {this.createTimeOptions()}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div className="section">
                            <h3>Truck Location</h3>
                            <p>{this.getLocation().formatted_address}</p>
                            <p>Serving until {formatRelative(endDateTime, new Date())}</p>
                        </div>
                        <hr />
                        <div className="section">
                            <h3>Summary</h3>
                            <ul className="checkout-items">
                                {this.state.items.map((item, key) =>
                                    <li className="checkout-item" key={`checkout-item-${key}`}>
                                        <div className="checkout-item-details">
                                            <div className="checkout-item-quantity">
                                                1
                                            </div>
                                            <div className="checkout-item-name">
                                                {item.name}
                                            </div>
                                            <div className="checkout-item-price">
                                                ${calculateItemPrice(item)}
                                            </div>
                                        </div>
                                        <div>
                                            <ul className="checkout-item-modifier-list">
                                                {item.modifiers.map((modifier, key) =>
                                                    <li key={`checkout-item-modifier-${key}`}>{modifier.name}</li>
                                                )}
                                            </ul>
                                        </div>
                                    </li>
                                )}
                            </ul>
                        </div>
                        <div className="section">
                            <h3>Payment</h3>
                            <Square />
                        </div>
                        <button className="button-checkout">Place Order</button>
                        <div className="button-summary">Total <span className="pull-right">${this.state.order.grand_total}</span>
                        </div>
                    </div>
                </div>
                <div className="checkout-summary">
                    <div className="checkout-summary-content">
                        <p>Order from: <strong>{this.state.truck.name}</strong></p>
                        <button className="button-checkout">Place Order</button>
                        <table>
                            <tbody>
                                <tr>
                                    <th align="left">Subtotal</th>
                                    <td align="right">${this.state.order.sub_total}</td>
                                </tr>
                                <tr>
                                    <th align="left">Taxes</th>
                                    <td align="right">${this.state.order.tax_total}</td>
                                </tr>
                            </tbody>
                        </table>
                        <table>
                            <tbody>
                                <tr>
                                    <th align="left">Tip</th>
                                    <td align="right">${this.state.order.tip}</td>
                                </tr>
                                <tr>
                                    <td colSpan={2} className="tip-row">
                                        <div className="btn-group">
                                            <label className={`btn ${!this.state.custom_tip && this.tipActive(0.00) ? 'active' : ''}`}>
                                                <input onChange={this.handleTipChange} name="tip" value="0" type="radio" /> None
                                            </label>
                                            <label className={`btn ${!this.state.custom_tip && this.tipActive((this.state.order.sub_total * .10).toFixed(2)) ? 'active' : ''}`}>
                                                <input onChange={this.handleTipChange} name="tip" value={(this.state.order.sub_total * .10).toFixed(2)} type="radio" /> ${(this.state.order.sub_total * .10).toFixed(2)}
                                            </label>
                                            <label className={`btn ${!this.state.custom_tip && this.tipActive((this.state.order.sub_total * .15).toFixed(2)) ? 'active' : ''}`}>
                                                <input onChange={this.handleTipChange} name="tip" value={(this.state.order.sub_total * .15).toFixed(2)} type="radio" /> ${(this.state.order.sub_total * .15).toFixed(2)}
                                            </label>
                                            <label className={`btn ${!this.state.custom_tip && this.tipActive((this.state.order.sub_total * .20).toFixed(2))  ? 'active' : ''}`}>
                                                <input onChange={this.handleTipChange} name="tip" value={(this.state.order.sub_total * .20).toFixed(2)} type="radio" /> ${(this.state.order.sub_total * .20).toFixed(2)}
                                            </label>
                                            <label className={`btn ${this.state.custom_tip ? 'active' : ''}`}>
                                                <input onChange={this.handleTipChange} name="tip" value="other" type="radio" /> Other
                                            </label>
                                        </div>
                                            {this.state.custom_tip &&
                                                <div className="custom-tip-wrapper">
                                                    <span className="custom-tip-label">$</span>
                                                    <input type="text" className="custom-tip" value={this.state.input.tip} onChange={this.handleTipChange}/>
                                                </div>
                                            }
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colSpan="2">100% of the tips goes to the food truck.</td>
                                </tr>
                            </tfoot>
                        </table>
                        <table>
                            <tbody>
                                <tr className="total">
                                    <th align="left">Total</th>
                                    <td align="right">${this.calculateGrandTotal()}</td>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div>
            </React.Fragment>
        )
    }
}

CheckoutPage.contextType = CartContext;

export default CheckoutPage;
