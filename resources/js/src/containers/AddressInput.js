import React, {Component} from "react";
import axios from 'axios';
import {
    withRouter
} from "react-router-dom";
import AddressInputDropdown from './AddressInputDropdown';
import queryString from 'query-string';
import {debounce} from 'lodash';

const utilizeFocus = () => {
    const ref = React.createRef();
    const setFocus = () => {
        ref.current && ref.current.focus()
    };
    return {setFocus, ref}
};

const getSuggestions = suggestions => {
    return suggestions;
};

class AddressInput extends Component {

    constructor(props) {
        super(props);
        this.state = {
            confirmedAddress: {
                label: 'Enter an address',
            },
            coordinates: {},
            showAddressInput: false,
            selectedAddress: null,
            showMap: false,
            value: '',
            suggestions: [],

        };
        this.$address = utilizeFocus();
        this.onSuggestionsFetchRequested1 = debounce(this.onSuggestionsFetchRequested,250);
    }

    toggleMap(state) {
        this.setState({
            showMap: typeof state !== 'undefined' ? state : !this.state.showMap
        })
    }

    setFocus() {
        this.$address.setFocus();
    }

    closeAddress(state) {
        this.setState({
            showAddressInput: false,
        });
    }

    onChange(event, {newValue}) {
        this.setState({
            value: newValue
        });
    };

    onSuggestionsFetchRequested({value}) {
        axios.get(`/api/search/suggestions?term=${value}`)
            .then((response) => {
                const data = response.data;
                this.setState({
                    suggestions: getSuggestions(data)
                });
            })
    };

    onSuggestionsClearRequested() {
        this.setState({
            suggestions: []
        });
    };

    onSuggestionSelected(event, {suggestion}) {
        this.setState({
            selectedAddress: suggestion,
        }, () => {
            axios.get(`/api/search`, {
                params: {
                    place_id: suggestion.value
                },
            }).then((response) => {
                this.setState({
                    coordinates: {
                        latitude: response.data.latitude,
                        longitude: response.data.longitude,
                    },
                    selectedAddress: response.data
                });
                this.toggleMap(true);
            });
        });
    }

    clearAddressInput() {
        this.setState({
            value: ''
        }, () => {
            this.setFocus();
        });
    }

    confirmAddress() {
        this.setState({
            confirmedAddress: this.state.selectedAddress
        }, () => {
            const params = queryString.stringify({
                latitude: this.state.confirmedAddress.latitude,
                longitude: this.state.confirmedAddress.longitude,
                geohash: this.state.confirmedAddress.geohash,
            });
            this.props.history.push({
                pathname: '/search',
                search: `?${params}`
            })
        });
    }

    render() {
        const {value, suggestions} = this.state;
        const inputProps = {
            id: 'input-address',
            placeholder: 'Address',
            value,
            onChange: this.onChange.bind(this),
            ref: this.$address.ref,
        };
        return (
            <AddressInputDropdown
                coordinates={this.state.coordinates}
                confirmedAddress={this.state.confirmedAddress}
                selectedAddress={this.state.selectedAddress}
                showMap={this.state.showMap}
                showAddressInput={this.state.showAddressInput}
                $address={this.state.$address}
                confirmAddress={this.confirmAddress.bind(this)}
                toggleMap={this.toggleMap.bind(this)}
                setFocus={this.setFocus.bind(this)}
                suggestions={suggestions}
                clearAddressInput={this.clearAddressInput.bind(this)}
                onSuggestionSelected={this.onSuggestionSelected.bind(this)}
                onSuggestionsFetchRequested={this.onSuggestionsFetchRequested1.bind(this)}
                onSuggestionsClearRequested={this.onSuggestionsClearRequested.bind(this)}
                inputProps={inputProps}
            />

        );
    }
}

export default withRouter(AddressInput);
