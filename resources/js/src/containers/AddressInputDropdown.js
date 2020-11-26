import React from 'react';
import Autosuggest from 'react-autosuggest';
import useComponentVisible from '../hooks/useComponentVisible';
import AddressMap from './AddressMap';

const getSuggestions = suggestions => {
    return suggestions;
};

const getSuggestionValue = suggestion => suggestion.label;


const renderSuggestion = suggestion => (
    <div>{suggestion.label}</div>
);

const getCoordinates = cords => {
    console.log(cords)

    return cords;
}

const AddressInputDropdown = (props) => {
    const {
        ref,
        isComponentVisible,
        setIsComponentVisible
    } = useComponentVisible(false);

    function onSuggestionSelected(event, data) {
        return props.onSuggestionSelected(event, data);
    }

    function toggleDropdown() {
        if (isComponentVisible) {
            props.clearAddressInput();
            props.toggleMap(false);
        }
        isComponentVisible ? setIsComponentVisible(false) : setIsComponentVisible(true);
        setTimeout(() => {
            props.setFocus();
        }, 10)
    }

    function confirmAddress() {
        props.confirmAddress();
        toggleDropdown();
    }

    return (
        <li className={`address-wrapper ${isComponentVisible ? 'active' : ''}`} ref={ref}>
            <div className="address-inner">
                <span className="address" onClick={toggleDropdown}>
                    <i className="icon ion-ios-pin"></i>
                    <span>{props.confirmedAddress.label}</span>
                    <i className="icon ion-ios-arrow-down"></i>
                    <i className="icon ion-ios-arrow-up"></i>
                </span>
            </div>
            {isComponentVisible && (
                <div className='dropdown'>
                    <label htmlFor="input-address">Search for an address</label>
                    <form className="dropdown--input" autoComplete="off">
                        <i className="icon ion-ios-pin"></i>
                        <Autosuggest
                            suggestions={props.suggestions}
                            onSuggestionSelected={onSuggestionSelected}
                            onSuggestionsFetchRequested={props.onSuggestionsFetchRequested}
                            onSuggestionsClearRequested={props.onSuggestionsClearRequested}
                            getSuggestionValue={getSuggestionValue}
                            renderSuggestion={renderSuggestion}
                            inputProps={props.inputProps}
                            tabindex={1}
                        />
                        <i className="icon ion-ios-close" onClick={props.clearAddressInput}
                           style={{display: props.inputProps.value.length === 0 ? 'none' : 'block'}}></i>
                    </form>
                    { props.showMap &&  <div className="dropdown-preview">
                        <AddressMap
                            coordinates={getCoordinates(props.coordinates)}
                            googleMapURL={`https://maps.googleapis.com/maps/api/js?key=${googleApiKey}`}
                            loadingElement={<div style={{height: `100%`}}/>}
                            containerElement={<div className="dropbox-map" style={{height: `200px`}}/>}
                            mapElement={<div style={{height: `100%`}}/>}
                        />
                        <button onClick={confirmAddress}>Done</button>
                    </div>
                    }


                </div>
            )}
        </li>
    );
};

export default AddressInputDropdown;
