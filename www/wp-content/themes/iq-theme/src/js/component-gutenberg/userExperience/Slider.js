import axios from 'axios';

const { registerBlockType } = wp.blocks;
const { Component } = wp.element;
const { InspectorControls } = wp.editor;
const { TextControl, ToggleControl, ServerSideRender } = wp.components;

registerBlockType('iq/slider', {
    title: 'Slider',
    icon: 'businessman',
    category: 'inqool-blocks',
    keywords: [
        'Lokality',
        'Slider'
    ],

    attributes: {
        count: {
            type: "integer",
            default: 0
        },
    },

    edit: class extends Component{

        constructor(props){
            super(...arguments);
            this.props = props;
 
        }

        render(){

            const { setAttributes, attributes: { count = 0, showFilter } = {} } = this.props;


            return(
                <div>
                    <ServerSideRender
                        block={ "iq/slider" }
                        attributes={ this.props.attributes }
                    />
                </div>
            )
        }
    },

    save: () => {
        return null;
    }
});