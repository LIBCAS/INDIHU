import axios from 'axios';

const { registerBlockType } = wp.blocks;
const { Component } = wp.element;
const { InspectorControls } = wp.editor;
const { SelectControl, TextControl, ToggleControl, ServerSideRender } = wp.components;

registerBlockType('iq/posts', {
    title: 'Aktuality',
    icon: 'admin-post',
    category: 'inqool-blocks',
    keywords: [
        'Články',
        'Aktuality',
        'Novinky',
    ],

    attributes: {
        selectedCategories: {
            type: 'array',
            default: []
        },
        count: {
            type: "integer",
            default: 0
        },
        showPagination: {
            type: "boolean",
            default: true
        },
        showNext: {
            type: "boolean",
            default: true
        },
        archivLink: {
            type: "string",
            default: ""
        }
    },

    edit: class extends Component{

        constructor(props){
            super(...arguments);
            this.props = props;

            this.state = {
                categories: []
            }

            this.handleCategoriesChange = this.handleCategoriesChange.bind(this);
        }

        componentDidMount() {
            axios.get('/api/post/categories')
                .then(({data = {} } = {} ) => {
                    this.setState({ 
                        categories: data 
                    });
                });
        }

        handleCategoriesChange(categories){
            this.props.setAttributes({
                selectedCategories: categories
            });
        }

        render(){

            const { setAttributes, attributes: { count = 0, showNext, showPagination, archivLink = "" } = {} } = this.props;

            return(
                <div>
                    <ServerSideRender
                        block={ "iq/posts" }
                        attributes={ this.props.attributes }
                    />
                    <InspectorControls key='inspector'>
                        <SelectControl 
                            multiple
                            value={ this.props.attributes.selectedCategories } 
                            label={ 'Vyberte kategorii' } 
                            options={ this.state.categories.map(category => ({value: category.id, label: category.name})) } 
                            onChange={ this.handleCategoriesChange }
                        />
                        <TextControl
                            label="Počet aktualit"
                            help="Při počtu nula se zobrazí všechny dostupné aktuality"
                            value={ count }
                            type="number"
                            onChange={ ( count ) => setAttributes( { count: parseInt(count) } ) }
                        />
                        <ToggleControl
                            label='Zobrazit stránkování'
                            checked={ showPagination }
                            onChange={ ( showPagination ) => setAttributes( { showPagination: showPagination } ) }
                        />
                        <ToggleControl
                            label='Zobrazit tlačítko "Více aktualit"'
                            checked={ showNext }
                            onChange={ ( showNext ) => setAttributes( { showNext: showNext } ) }
                        />
                        {showNext && (
                            <TextControl
                                label="Více aktualit - url"
                                value={ archivLink }
                                type="url"
                                onChange={ ( link ) => setAttributes( { archivLink: link } ) }
                            />
                        )}
                    </InspectorControls>
                </div>
            )
        }
    },

    save: () => {
        return null;
    }
});