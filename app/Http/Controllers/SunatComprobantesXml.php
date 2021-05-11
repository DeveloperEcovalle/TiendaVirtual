<?php

namespace App\Http\Controllers;

use App\SunatFacturaBoleta;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Report\HtmlReport;
use Greenter\Report\PdfReport;
use Greenter\Ws\Services\BillSender;
use Greenter\Ws\Services\SoapClient;
use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;
use Greenter\XMLSecLibs\Sunat\SignedXml;
use Illuminate\Support\Facades\Storage;

abstract class SunatComprobantesXml {

    private static $URL_SERVICE_TEST = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
    private static $URL_SERVICE_PROD = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';

    private static $FACTURA = 'FACTURA';
    private static $BOLETA = 'BOLETA';
    private static $NOTA_CREDITO = 'NOTA_CREDITO';
    private static $NOTA_DEBITO = 'NOTA_DEBITO';
    private static $COMUNICACION_BAJA = 'COMUNICACION_BAJA';
    private static $RESUMEN_DIARIO = 'RESUMEN_DIARIO';
    private static $COMPROBANTE_PERCEPCION = 'COMPROBANTE_PERCEPCION';
    private static $COMPROBANTE_RETENCION = 'COMPROBANTE_RETENCION';
    private static $RESUMEN_DIARIO_REVERSION = 'RESUMEN_DIARIO_REVERSION';
    private static $GUIA_REMISION = 'GUIA_REMISION';
    private static $LOTE_FACTURAS_NOTAS = 'LOTE_FACTURAS_NOTAS';

    public static function crearArchivoXmlFacturaBoleta(SunatFacturaBoleta $sunatFacturaBoleta) {
        $sContenidoXml = '';

        //region ESTRUCTURA INICIAL
        $_00_cabecera = '<?xml version="1.0" encoding="utf-8"?>
         <Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
         xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
         xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
         xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
         xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">';

        $_01_FirmaDigital = '';
        $_02_VersionUBL = '';
        $_03_VersionEstructuraDcto = '';
        $_04_CodigoTipoOperacion = '';//CATALOGO 17
        $_05_SerieNroComprobante = '';
        $_06_FechaEmision = '';
        $_07_HoraEmision = '';
        $_08_FechaVencimiento = '';
        $_09_TipoComprobante = '';//CATALOGO 1
        $_10_Leyenda = '';
        $_11_TipoMoneda = '';
        $_12_GuiaRemisionRelacionada = '';
        $_13_DctoRelacionado = '';
        $_14_15_16_17_DatosEmisor = '';
        $_18_19_DatosCliente = '';
        $_20_DatosUbigeo = '';
        $_21_DescuentosGlobales = '';
        $_22_23_24_25_26_27_28_29_DatosImpuestos = '';
        $_30_31_32_33_34_DatosMontosVenta = '';
        // INI DETALLES
        $_35_36_37_DatosItems = '';
        $_38_PrecioUnitarioxItem = '';
        $_39_ValorReferencialUnitarioxItem = '';
        $_40_DescuentosxItem = '';
        $_41_CargosxItem = '';
        $_42_AfectacionIGVxItem = '';
        $_43_AfectacionISCxItem = '';
        $_44_DescripcionDetalladaProducto = '';
        $_45_46_CodigoProducto = '';
        $_47_DatosAdicionalesxItem = '';
        $_48_ValorUnitarioxItem = '';
        $_99_Final = '</Invoice>';
        //endregion

        //region FIRMA DIGITAL
        $_01_FirmaDigital = '<ext:UBLExtensions>
        <ext:UBLExtension>
        <ext:ExtensionContent></ext:ExtensionContent>
        </ext:UBLExtension>
        </ext:UBLExtensions>';
        //endregion

        //region VERSION UBL
        $_02_VersionUBL = '<cbc:UBLVersionID>2.1</cbc:UBLVersionID>';
        //endregion

        //region VERSION ESTRUCTURA DEL DOCUMENTO
        $_03_VersionEstructuraDcto = '<cbc:CustomizationID schemeAgencyName="PE:SUNAT">2.0</cbc:CustomizationID>';
        $sTipoOperacion = '';
        if ($sunatFacturaBoleta->tipo_operacion === '04') {
            //04-ANTICIPO
            $sTipoOperacion = '0104';
        } else {
            //01-VENTA INTERNA
            $sTipoOperacion = '0101';
            //1001 Operación Sujeta a Detracción
        }
        //endregion

        //TODO 20191030 Habilitar uso cuando las funcionalidades complementarias estén implementadas
        $_04_CodigoTipoOperacion = '<cbc:ProfileID schemeName="Tipo de Operacion" schemeAgencyName="PE:SUNAT"
        schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo51">0101</cbc:ProfileID>';
        //*************************************************************
        $_05_SerieNroComprobante = '<cbc:ID>' . $sunatFacturaBoleta->serie_comprobante . "-" . $sunatFacturaBoleta->nro_comprobante . '</cbc:ID>'; //F001-100
        //*************************************************************
        $_06_FechaEmision = '<cbc:IssueDate>' . $sunatFacturaBoleta->fecha_emision . '</cbc:IssueDate>'; //FORMATO: 2017-04-28
        //*************************************************************
        $_07_HoraEmision = '<cbc:IssueTime>' . $sunatFacturaBoleta->hora_emision . '</cbc:IssueTime>'; //FORMATO: 11:40:21
        //*************************************************************
        $_08_FechaVencimiento = '<cbc:DueDate>' . $sunatFacturaBoleta->fecha_emision . '</cbc:DueDate>'; //FORMATO: 2017-04-28
        //*************************************************************
        $_09_TipoComprobante = '<cbc:InvoiceTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Documento"
        listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01" listID="0101" name="Tipo de Operacion"
        listSchemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo51">' . $sunatFacturaBoleta->tipo_comprobante . '</cbc:InvoiceTypeCode>'; //CATALOGO 1

        //region LEYENDA
        //CATALOGO 52
        //1000: Monto en letras
        //1002: Leyenda "TRANSFERENCIA GRATUITA DE UN BIEN Y/O SERVICIO PRESTADO GRATUITAMENTE"
        //2000: Leyenda "COMPROBANTE DE PERCEPCIÓN"
        //...
        //2004: Leyenda "Agencia de Viaje - Paquete turístico"
        //2005: Leyenda "Venta realizada por emisor itinerante"
        //2006: Leyenda "Operación sujeta a detracción"
        $_10_Leyenda = '<cbc:Note languageLocaleID="1000">' . $sunatFacturaBoleta->leyenda_descripcion . '</cbc:Note>';
        if ($sunatFacturaBoleta->leyenda_descripcion === '') {
            $_10_Leyenda = '';
        }
        //endregion

        $_11_TipoMoneda = '<cbc:DocumentCurrencyCode listID="ISO 4217 Alpha" listName="Currency" listAgencyName="United Nations Economic Commission for Europe">PEN</cbc:DocumentCurrencyCode>';

        if (false) {//NO PARA ECOVALLE //SUNAT SI LO ACEPTA
            $_12_GuiaRemisionRelacionada = '<cac:DespatchDocumentReference>
                <cbc:ID>031-002020</cbc:ID>
                <cbc:DocumentTypeCode listAgencyName="PE:SUNAT"
                listName="SUNAT:Identificador de guía relacionada"
                listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">09</cbc:DocumentTypeCode>
                </cac:DespatchDocumentReference>';
        }

        $sSignature = '<cac:Signature>
            <cbc:ID>' . $sunatFacturaBoleta->serie_comprobante . '-' . $sunatFacturaBoleta->nro_comprobante . '</cbc:ID>
            <cac:SignatoryParty>
            <cac:PartyIdentification>
            <cbc:ID>' . $sunatFacturaBoleta->empresa_numero_ruc . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
            <cbc:Name>' . $sunatFacturaBoleta->razon_social . '</cbc:Name>
            </cac:PartyName>
            </cac:SignatoryParty>
            <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
            <cbc:URI>#' . $sunatFacturaBoleta->serie_comprobante . '-' . $sunatFacturaBoleta->nro_comprobante . '</cbc:URI>
            </cac:ExternalReference>
            </cac:DigitalSignatureAttachment>
            </cac:Signature>';
        //EN GUIA SUNAT NO APARECE EL TAG "cac:PartyIdentification" (OCASIONA ERROR SI FALTA)

        //region DATOS DEL EMISOR
        $sAddressLine = '';
        $sPostalAdresss = '';
        if ($sunatFacturaBoleta->domicilio_ubigeo) {
            $sAddressLine = $sunatFacturaBoleta->domicilio_direccion_detallada . ' '
                . $sunatFacturaBoleta->domicilio_departamento . '-'
                . $sunatFacturaBoleta->domicilio_provincia . '-'
                . $sunatFacturaBoleta->domicilio_distrito;

            $sPostalAdresss .= '<cac:PostalAddress>
                <cbc:ID>' . $sunatFacturaBoleta->domicilio_ubigeo . '</cbc:ID>
                <cbc:AddressTypeCode listAgencyName="PE:SUNAT" listName="Establecimientos anexos">0000</cbc:AddressTypeCode>
                <cbc:StreetName><![CDATA[' . $sunatFacturaBoleta->domicilio_direccion_detallada . ']]></cbc:StreetName>
                <cbc:CitySubdivisionName><![CDATA[' . $sunatFacturaBoleta->domicilio_urbanizacion . ']]></cbc:CitySubdivisionName>
                <cbc:CityName><![CDATA[' . $sunatFacturaBoleta->domicilio_provincia . ']]></cbc:CityName>
                <cbc:CountrySubentity><![CDATA[' . $sunatFacturaBoleta->domicilio_departamento . ']]></cbc:CountrySubentity>
                <cbc:District><![CDATA[' . $sunatFacturaBoleta->domicilio_distrito . ']]></cbc:District>
                <cac:AddressLine>
                <cbc:Line><![CDATA[' . $sAddressLine . ']]></cbc:Line>
                </cac:AddressLine>
                <cac:Country>
                <cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country">PE</cbc:IdentificationCode>
                </cac:Country>
                </cac:PostalAddress>';
        }

        $_14_15_16_17_DatosEmisor = '<cac:AccountingSupplierParty>
            <cac:Party>
            <cac:PartyIdentification>
            <cbc:ID schemeID="6" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $sunatFacturaBoleta->empresa_numero_ruc . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
            <cbc:Name><![CDATA[' . $sunatFacturaBoleta->nombre_comercial . ']]></cbc:Name>
            </cac:PartyName>' . $sPostalAdresss . '<cac:PartyTaxScheme>
            <cbc:RegistrationName><![CDATA[' . $sunatFacturaBoleta->razon_social . ']]></cbc:RegistrationName>
            <cbc:CompanyID schemeID="6" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $sunatFacturaBoleta->empresa_numero_ruc . '</cbc:CompanyID>
            <cac:TaxScheme>
            <cbc:ID schemeID="6" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $sunatFacturaBoleta->empresa_numero_ruc . '</cbc:ID>
            </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
            <cbc:RegistrationName><![CDATA[' . $sunatFacturaBoleta->razon_social . ']]></cbc:RegistrationName>
            <cac:RegistrationAddress>
            <cbc:AddressTypeCode listAgencyName="PE:SUNAT" listName="Establecimientos anexos">0000</cbc:AddressTypeCode>
            </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
            <cac:Contact>
            <cbc:Name/>
            </cac:Contact>
            </cac:Party>
            </cac:AccountingSupplierParty>';
        //endregion

        //region DATOS DEL CLIENTE
        $sRazonSocialCliente = $sunatFacturaBoleta->razon_social_cliente;
        $sNroDocumentoCliente = $sunatFacturaBoleta->nro_documento_cliente;

        $_18_19_DatosCliente = '<cac:AccountingCustomerParty>
            <cac:Party>
            <cac:PartyIdentification>
            <cbc:ID schemeID="' . $sunatFacturaBoleta->tipo_documento_cliente . '" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $sNroDocumentoCliente . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
            <cbc:Name><![CDATA[' . $sRazonSocialCliente . ']]></cbc:Name>
            </cac:PartyName>
            <cac:PartyTaxScheme>
            <cbc:RegistrationName><![CDATA[' . $sRazonSocialCliente . ']]></cbc:RegistrationName>
            <cbc:CompanyID schemeID="' . $sunatFacturaBoleta->tipo_documento_cliente . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $sNroDocumentoCliente . '</cbc:CompanyID>
            <cac:TaxScheme>
            <cbc:ID schemeID="' . $sunatFacturaBoleta->tipo_documento_cliente . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $sNroDocumentoCliente . '</cbc:ID>
            </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
            <cbc:RegistrationName><![CDATA[' . $sRazonSocialCliente . ']]></cbc:RegistrationName>
            <cac:RegistrationAddress>
            <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI"></cbc:ID>
            <cbc:CityName><![CDATA[]]></cbc:CityName>
            <cbc:CountrySubentity><![CDATA[]]></cbc:CountrySubentity>
            <cbc:District><![CDATA[]]></cbc:District>
            <cac:AddressLine>
            <cbc:Line><![CDATA[' . $sunatFacturaBoleta->direccion_cliente . ']]></cbc:Line>
            </cac:AddressLine>
            <cac:Country>
            <cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country">PE</cbc:IdentificationCode>
            </cac:Country>
            </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
            </cac:Party>
            </cac:AccountingCustomerParty>';
        //endregion

        //region DATOS UBIGEO
        if (false) {//NO PARA CITYO, TODO PARA VENTA ITINERANTE //LUGAR DE ENTREGA DEL CONTRIBUYENTE
            //SUNAT SI LO ACEPTA
            $_20_DatosUbigeo = '<cac:DeliveryTerms>
            <cac:DeliveryLocation >
            <cac:Address>
            <cbc:StreetName>CALLE NEGOCIOS # 420</cbc:StreetName>
            <cbc:CitySubdivisionName/>
            <cbc:CityName>LIMA</cbc:CityName>
            <cbc:CountrySubentity>LIMA</cbc:CountrySubentity>
            <cbc:CountrySubentityCode>150141</cbc:CountrySubentityCode>
            <cbc:District>SURQUILLO</cbc:District>
            <cac:Country>
            <cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country">PE</cbc:IdentificationCode>
            </cac:Country>
            </cac:Address>
            </cac:DeliveryLocation>
            </cac:DeliveryTerms>';
        }
        //endregion

        //region DESCUENTOS GLOBALES
        if ($sunatFacturaBoleta->descuentos_globales > 0) {
            $_21_DescuentosGlobales = '<cac:AllowanceCharge>
            <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
            <cbc:AllowanceChargeReasonCode listAgencyName="PE:SUNAT" listName="Cargo/descuento"  listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo53">02</cbc:AllowanceChargeReasonCode>
            <cbc:MultiplierFactorNumeric>' . number_format($sunatFacturaBoleta->porcentaje_descuentos_globales / 100, '5', '.', '') . '</cbc:MultiplierFactorNumeric>
            <cbc:Amount currencyID="PEN">' . number_format($sunatFacturaBoleta->descuentos_globales, 2, '.', '') . '</cbc:Amount>
            <cbc:BaseAmount currencyID="PEN">' . number_format($sunatFacturaBoleta->total_valor_venta_neto, 2, '.', '') . '</cbc:BaseAmount>
            </cac:AllowanceCharge>';
        }
        //endregion

        //SUNAT NO LO ACEPTA TAL CUAL(ACEPTA 1 SUBTOTAL - TaxSubtotal)
        //region DATOS IMPUESTOS
        $_22_23_24_25_26_27_28_29_DatosImpuestos = '<cac:TaxTotal>
        <cbc:TaxAmount currencyID="PEN">' . number_format($sunatFacturaBoleta->sumatoria_igv_monto_1 + $sunatFacturaBoleta->sumatoria_icbper_monto, 2, '.', '') . '</cbc:TaxAmount>';

        if ($sunatFacturaBoleta->total_valor_venta_gravada_monto > 0) {
            $_22_23_24_25_26_27_28_29_DatosImpuestos .= '<cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="PEN">' . number_format($sunatFacturaBoleta->total_valor_venta_gravada_monto, 2, '.', '') . '</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="PEN">' . number_format($sunatFacturaBoleta->sumatoria_igv_monto_1, 2, '.', '') . '</cbc:TaxAmount>
            <cac:TaxCategory>
            <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
            <cac:TaxScheme>
            <cbc:ID schemeID="UN/ECE 5305" schemeAgencyID="6">1000</cbc:ID>
            <cbc:Name>IGV</cbc:Name>
            <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
            </cac:TaxScheme>
            </cac:TaxCategory>
            </cac:TaxSubtotal>';
        }

        if ($sunatFacturaBoleta->total_valor_venta_exonerada_monto > 0) {
            $_22_23_24_25_26_27_28_29_DatosImpuestos .= '<cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="PEN">' . number_format($sunatFacturaBoleta->total_valor_venta_exonerada_monto, 2, '.', '') . '</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="PEN">0.00</cbc:TaxAmount>
            <cac:TaxCategory>
            <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
            <cac:TaxScheme>
            <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
            <cbc:Name>EXO</cbc:Name>
            <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
            </cac:TaxScheme>
            </cac:TaxCategory>
            </cac:TaxSubtotal>';
        }

        if ($sunatFacturaBoleta->total_valor_venta_inafecta_monto > 0) {
            $_22_23_24_25_26_27_28_29_DatosImpuestos .= '<cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="PEN">' . number_format($sunatFacturaBoleta->total_valor_venta_inafecta_monto, 2, '.', '') . '</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="PEN">0.00</cbc:TaxAmount>
            <cac:TaxCategory>
            <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
            <cac:TaxScheme>
            <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
            <cbc:Name>INA</cbc:Name>
            <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
            </cac:TaxScheme>
            </cac:TaxCategory>
            </cac:TaxSubtotal>';
        }

        if ($sunatFacturaBoleta->sumatoria_icbper_monto > 0) {
            $_22_23_24_25_26_27_28_29_DatosImpuestos .= '<cac:TaxSubtotal>
            <cbc:TaxAmount currencyID="PEN">' . number_format($sunatFacturaBoleta->sumatoria_icbper_monto, 2, '.', '') . '</cbc:TaxAmount>
            <cac:TaxCategory>
            <cac:TaxScheme>
            <cbc:ID>7152</cbc:ID>
            <cbc:Name>ICBPER</cbc:Name>
            <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
            </cac:TaxScheme>
            </cac:TaxCategory>
            </cac:TaxSubtotal>';
        }

        $_22_23_24_25_26_27_28_29_DatosImpuestos .= '</cac:TaxTotal>';
        //endregion

        //region DATOS MONTOS VENTA
        $fTotalFinalValorVenta = $sunatFacturaBoleta->total_valor_venta_neto - $sunatFacturaBoleta->descuentos_globales;
        $fSubtotalVenta = $fTotalFinalValorVenta + $sunatFacturaBoleta->sumatoria_igv_monto_1 + $sunatFacturaBoleta->sumatoria_isc_monto_1 + $sunatFacturaBoleta->sumatoria_icbper_monto;
        $sRedondeo = '';

        if ($sunatFacturaBoleta->monto_redondeo > 0) {
            $sRedondeo = '<cbc:PayableRoundingAmount currencyID="PEN">' . number_format($sunatFacturaBoleta->monto_redondeo, 2, '.', '') . '</cbc:PayableRoundingAmount>';
        }

        $_30_31_32_33_34_DatosMontosVenta = '<cac:LegalMonetaryTotal>
            <cbc:LineExtensionAmount currencyID="PEN">' . number_format($fTotalFinalValorVenta, 2, '.', '') . '</cbc:LineExtensionAmount>
            <cbc:TaxInclusiveAmount currencyID="PEN">' . number_format($fSubtotalVenta, 2, '.', '') . '</cbc:TaxInclusiveAmount>
            <cbc:AllowanceTotalAmount currencyID="PEN">0</cbc:AllowanceTotalAmount>
            <cbc:ChargeTotalAmount currencyID="PEN">' . number_format($sunatFacturaBoleta->sumatoria_otros_cargos, 2, '.', '') . '</cbc:ChargeTotalAmount>' . $sRedondeo .
            '<cbc:PayableAmount currencyID="PEN">' . number_format($sunatFacturaBoleta->importe_total_venta - $sunatFacturaBoleta->monto_redondeo) . '</cbc:PayableAmount>
            </cac:LegalMonetaryTotal>';
        //endregion

        /*TOTAL VALOR VENTA: [LineExtensionAmount] A través de este elemento se debe indicar
        el valor de venta total de la operación.  Es decir el importe total de la venta
        sin considerar los descuentos, impuestos u otros tributos a que se refiere
        el numeral anterior, pero que incluye cualquier monto de redondeo aplicable.
        The monetary amount of an extended transaction line, net of tax and settlement discounts, but inclusive of any applicable rounding amount.
        TOTAL PRECIO DE VENTA: [TaxInclusiveAmount] A través de este elemento se debe indicar
        el valor de venta total de la operación incluido los impuestos.
        The monetary amount including taxes; the sum of payable amount and prepaid amount.
        TOTAL DESCUENTOS: [AllowanceTotalAmount] A través de este elemento se debe indicar
        el valor total de los descuentos globales realizados de ser el caso.
        Este elemento es distinto al elemento Descuentos Globales definido en el punto 35.
        Su propósito es permitir consignar en el comprobante de pago:
            - la sumatoria de los descuentos de cada línea (descuentos por ítem), o
            - la sumatoria de los descuentos de línea (ítem) + descuentos globales
        The total monetary amount of all allowances.
        SUMATORIA OTROS CARGOS: [ChargeTotalAmount]Corresponde al total de otros cargos cobrados al adquirente o usuario y que no forman
        parte de la operación que se factura, es decir no forman parte del(os) valor(es) de ventas señaladas anteriormente,
        pero sí forman parte del importe total de la Venta (Ejemplo: propinas, garantías para devolución de envases, etc.)
        IMPORTE TOTAL DE LA VENTA: [PayableAmount]  Corresponde al importe total de la venta, de la cesión en uso o del servicio prestado.
        Es el resultado de la suma y/o resta (Según corresponda) de los siguientes puntos:
        31-32+33 (Total Precio de Venta - Total de Descuentos + Sumatoria otros Cargos) menos los anticipos que hubieran sido recibidos.
        The amount of the monetary total to be paid.
        REDONDEO: Invoice/cac:LegalMonetaryTotal/cbc:PayableRoundingAmount
        Se podrá consignar la diferencia entre el importe total y el importe redondeado.
        The rounding amount (positive or negative) added to produce the line extension amount.*/

        $sGrupoDetalles = '';

        //TODO VALIDAR QUE TENGA AL MENOS 1 DETALLE
        //if ($sunatFacturaBoleta->getLstDetalles() != null) {

        //region XML DETALLES
        foreach ($sunatFacturaBoleta->detalles as $i => $detalle) {//TODO BORRAR LUEGO DE TEST
            $sDetalle = '<cac:InvoiceLine>';

            //region DATOS ITEMS
            $sCodigoUnidadMedidaSunat = $detalle->unidad_medida;//20190823
            if ($sCodigoUnidadMedidaSunat === '') {
                $sCodigoUnidadMedidaSunat = 'NIU';
            }

            $_35_36_37_DatosItems = '<cbc:ID>' . $detalle->numero_orden . '</cbc:ID>
            <cbc:InvoicedQuantity unitCode="' . $sCodigoUnidadMedidaSunat . '" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyName="United Nations Economic Commission for Europe">' . $detalle->cantidad . '</cbc:InvoicedQuantity>
            <cbc:LineExtensionAmount currencyID="PEN">' . number_format($detalle->valor_venta, 2, '.', '') . '</cbc:LineExtensionAmount>';
            //endregion

            $sDetalle .= $_35_36_37_DatosItems;

            //CATALOGO 16:
            // 01: PRECIO UNITARIO (INCLUYE IGV)
            // 02: VALOR REFERENCIAL UNITARIO EN OPERACIONES NO ONEROSAS (GRATUITOS)
            if ($detalle->afectacion_igv !== 'O') { //GRAVADOS, INAFECTOS Y EXONERADOS
                //region PRECIO UNITARIO POR ITEM
                $_38_PrecioUnitarioxItem = '<cac:PricingReference>
                <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID="PEN">' . number_format($detalle->precio_venta_unitario_monto, 2, '.', '') . '</cbc:PriceAmount>
                <cbc:PriceTypeCode listName="Tipo de Precio" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">01</cbc:PriceTypeCode>
                </cac:AlternativeConditionPrice>
                </cac:PricingReference>';
                //endregion

                $sDetalle .= $_38_PrecioUnitarioxItem;
            } else { //PARA DETALLES GRATIS
                //region VALOR REFERENCIAL UNITARIO POR ITEM
                $_39_ValorReferencialUnitarioxItem = '<cac:PricingReference>
                <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID="PEN">' . number_format($detalle->valor_unitario_oneroso, 2, '.', '') . '</cbc:PriceAmount>
                <cbc:PriceTypeCode listName="SUNAT:Indicador de Tipo de Precio" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">02</cbc:PriceTypeCode>
                </cac:AlternativeConditionPrice>
                </cac:PricingReference>';
                //endregion

                $sDetalle .= $_39_ValorReferencialUnitarioxItem;
            }
            //*************************************************************
            if ($detalle->descuento > 0) {//20200605
                //region DESCUENTOS POR ITEM
                $_40_DescuentosxItem = '<cac:AllowanceCharge>
                <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
                <cbc:AllowanceChargeReasonCode>00</cbc:AllowanceChargeReasonCode>
                <cbc:MultiplierFactorNumeric>' . number_format($detalle->porcentaje_descuento / 100, 5, '.', '') . '</cbc:MultiplierFactorNumeric>
                <cbc:Amount currencyID="PEN">' . number_format($detalle->descuento, 2, '.', '') . '</cbc:Amount>
                <cbc:BaseAmount currencyID="PEN">' . number_format($detalle->valor_venta_bruto, 2, '.', '') . '</cbc:BaseAmount>
                </cac:AllowanceCharge>';
                //endregion

                $sDetalle .= $_40_DescuentosxItem;
            } else if (false) {//NO PARA CITYO
                //region CARGOS POR ITEM
                $_41_CargosxItem = '<cac:AllowanceCharge>
                <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
                <cbc:AllowanceChargeReasonCode>50</cbc:AllowanceChargeReasonCode>
                <cbc:MultiplierFactorNumeric>0.10</cbc:MultiplierFactorNumeric>
                <cbc:Amount currencyID="PEN">44.82</cbc:Amount>
                <cbc:BaseAmount currencyID="PEN">448.20</cbc:BaseAmount>
                </cac:AllowanceCharge>';
                //endregion

                $sDetalle .= $_41_CargosxItem;
            }

            //region AFECTACION IGV POR ITEM
            $sAfectacionICBPERxItem = ''; //20200605
            if ($detalle->afectacion_icbper == 1 && $detalle->icbper_monto > 0) {
                $sAfectacionICBPERxItem = '<cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="PEN">' . number_format($detalle->icbper_monto, 2, '.', '') . '</cbc:TaxAmount>
                <cbc:BaseUnitMeasure unitCode="NIU">' . number_format($detalle->cantidad, 0) . '</cbc:BaseUnitMeasure>
                <cac:TaxCategory>
                <cbc:BaseUnitMeasure unitCode="NIU">1</cbc:BaseUnitMeasure>
                <cbc:PerUnitAmount currencyID="PEN">' . number_format($sunatFacturaBoleta->icbper_anio, 2, '.', '') . '</cbc:PerUnitAmount>
                <cac:TaxScheme>
                <cbc:ID>7152</cbc:ID>
                <cbc:Name>ICBPER</cbc:Name>
                <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                </cac:TaxScheme>
                </cac:TaxCategory>
                </cac:TaxSubtotal>';
            }

            //USA IGV 18 ESTATICO
            //Datos de llegan diferentes desde BD para productos GRAVADOS, INAFECTOS, EXONERADOS Y ONEROSOS
            //sp_ven_insertar_detalles_restantes_comprobante
            $_42_AfectacionIGVxItem = '<cac:TaxTotal>
                <cbc:TaxAmount currencyID="PEN">' . number_format($detalle->igv_monto_1 + $detalle->icbper_monto, 2, '.', '') . '</cbc:TaxAmount>
                <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="PEN">' . number_format($detalle->valor_venta, 2, '.', '') . '</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="PEN">' . number_format($detalle->igv_monto_1, 2, '.', '') . '</cbc:TaxAmount>
                <cac:TaxCategory>
                <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
                <cbc:Percent>' . ($detalle->igv_monto_1 > 0 ? '18.00' : '0.00') . '</cbc:Percent>
                <cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="Afectacion del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">' . $detalle->igv_codigo_tipo . '</cbc:TaxExemptionReasonCode>
                <cac:TaxScheme>
                <cbc:ID schemeID="UN/ECE 5153" schemeName="Codigo de tributos" schemeAgencyName="PE:SUNAT">' . $detalle->igv_codigo_tributo . '</cbc:ID>
                <cbc:Name>' . $detalle->igv_nombre_tributo . '</cbc:Name>
                <cbc:TaxTypeCode>' . $detalle->igv_codigo_tributo_internacional . '</cbc:TaxTypeCode>
                </cac:TaxScheme>
                </cac:TaxCategory>
                </cac:TaxSubtotal>' . $sAfectacionICBPERxItem . '</cac:TaxTotal>';
            //endregion

            $sDetalle .= $_42_AfectacionIGVxItem;

            //NO PARA CITYO
            //region AFECTACION ISC POR ITEM
            //SUNAT NO LO ACEPTA, TAMPOCO ACEPTARA 2 TaxTotal
            $_43_AfectacionISCxItem = '<cac:TaxTotal>
                <cbc:TaxAmount currencyID="PEN">1750.52</cbc:TaxAmount>
                <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="PEN">8752.60</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="PEN">1750.52</cbc:TaxAmount>
                <cac:TaxCategory>
                <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
                <cbc:Percent>20.00</cbc:Percent>
                <cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="SUNAT:Codigo de Tipo de Afectación del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">10</cbc:TaxExemptionReasonCode>
                <cac:TaxScheme>
                <cbc:ID schemeID="UN/ECE 5153" schemeName="Tax Scheme Identifier" schemeAgencyName="United Nations Economic Commission for Europe">2000</cbc:ID>
                <cbc:Name>ISC</cbc:Name>
                <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                </cac:TaxScheme>
                </cac:TaxCategory>
                </cac:TaxSubtotal>
                </cac:TaxTotal>';
            //endregion

            //sDetalle .= $_43_AfectacionISCxItem;

            //SUNAT DECIA  <cbc:SellersItemIdentification> (OCASIONA ERROR)
            //region CODIGO PRODUCTO
            $_45_46_CodigoProducto = '<cac:SellersItemIdentification>
                <cbc:ID><![CDATA[' . $detalle->codigo_producto . ']]></cbc:ID>
                </cac:SellersItemIdentification>' //  . "<cac:CommodityClassification>"
                //  . " <cbc:ItemClassificationCode listID="UNSPSC" listAgencyName="GS1 US" listName="Item Classification">51121703</cbc:ItemClassificationCode>"//Codigo producto de SUNAT
                //  . "</cac:CommodityClassification>"
            ;
            //endregion

            //region DATOS ADICIONALES POR ITEM
            //NO PARA CITYO
            //SUNAT SI LO ACEPTA
            $_47_DatosAdicionalesxItem = '<cac:AdditionalItemProperty>
            <cbc:Name>Gastos Art. 37 Renta: Número de Placa</cbc:Name>
            <cbc:NameCode listName="SUNAT :Identificador de la propiedad del ítem" listAgencyName="PE:SUNAT">7000</cbc:NameCode>
            <cbc:Value>B6F-045</cbc:Value>
            </cac:AdditionalItemProperty>';
            //endregion

            //WARNING: _44_DescripcionDetalladaProducto = _44_DescripcionDetalladaProducto . _45_46_CodigoProducto ._47_DatosAdicionalesxItem
            //region DESCRIPCION DETALLADA PRODUCTO
            $_44_DescripcionDetalladaProducto = '<cac:Item>
            <cbc:Description><![CDATA[' . $detalle->descripcion . ']]></cbc:Description>';

            if ($detalle->informacion_adicional && $detalle->informacion_adicional !== null) {
                $_44_DescripcionDetalladaProducto .= ' <cbc:AdditionalInformation>' . $detalle->informacion_adicional . '</cbc:AdditionalInformation>';
            }

            $_44_DescripcionDetalladaProducto .= $_45_46_CodigoProducto
                //   . _47_DatosAdicionalesxItem
                . '</cac:Item>';
            //endregion

            $sDetalle .= $_44_DescripcionDetalladaProducto;

            //region VALOR UNITARIO POR ITEM
            $_48_ValorUnitarioxItem = '<cac:Price>
            <cbc:PriceAmount currencyID="PEN">' . number_format($detalle->valor_unitario, 8, '.', '') . '</cbc:PriceAmount>
            </cac:Price>';
            //endregion

            $sDetalle .= $_48_ValorUnitarioxItem;

            $sDetalle .= '</cac:InvoiceLine>';
            $sGrupoDetalles .= $sDetalle;
        }
        //endregion

        //region UNIFICAR TODAS LAS PARTES DEL XML
        $sContenidoXml .= $_00_cabecera
            . $_01_FirmaDigital
            . $_02_VersionUBL
            . $_03_VersionEstructuraDcto
            . $_04_CodigoTipoOperacion
            . $_05_SerieNroComprobante
            . $_06_FechaEmision
            . $_07_HoraEmision
            . $_08_FechaVencimiento
            . $_09_TipoComprobante
            . $_10_Leyenda
            . $_11_TipoMoneda
            . '<cbc:LineCountNumeric>' . $sunatFacturaBoleta->detalles()->count() . '</cbc:LineCountNumeric>' //TODO CAMBIAR SEGUN NRO DE DETALLES
            . $_12_GuiaRemisionRelacionada
            . $_13_DctoRelacionado
            . $sSignature
            . $_14_15_16_17_DatosEmisor
            . $_18_19_DatosCliente
            . $_20_DatosUbigeo
            . $_21_DescuentosGlobales
            . $_22_23_24_25_26_27_28_29_DatosImpuestos
            . $_30_31_32_33_34_DatosMontosVenta
            . $sGrupoDetalles
            . $_99_Final;
        //endregion

        $sTipoComprobante = $sunatFacturaBoleta->tipo_comprobante === '01' ? self::$FACTURA : self::$BOLETA;
        $sNombreArchivo = self::generarNombreComprobante($sTipoComprobante, $sunatFacturaBoleta, '', '');
        $sRutaArchivo = 'comprobantes/' . $sNombreArchivo . '.xml';

        Storage::disk('public')->put($sRutaArchivo, $sContenidoXml);

        return $sNombreArchivo;
    }

    private static function generarNombreComprobante($tipoDocumentoSunat, SunatFacturaBoleta $sunatFacturaBoleta, $sFechaGeneracionArchivo, $sNroCorrelativo) {
        $sRucEmisor = $sunatFacturaBoleta->empresa_numero_ruc;
        $sCodigoTipoComprobante = $sunatFacturaBoleta->tipo_comprobante; //NN
        $sSerieComprobante = $sunatFacturaBoleta->serie_comprobante; //FNNN
        $sNroComprobante = $sunatFacturaBoleta->nro_comprobante;

        //OTROS para REEMPLAZAR //TODO OBTENER FECHA Y CORRELATIVO PARA LOS COMPROBANTES QUE LO USEN
        // $sFechaGeneracionArchivo = "20180911";//YYYYMMDD
        // $sNroCorrelativo = "1";//[NNNNN]

        $sNombreArchivo = '';

        if ($tipoDocumentoSunat == self::$FACTURA
            || $tipoDocumentoSunat == self::$BOLETA
            || $tipoDocumentoSunat == self::$NOTA_CREDITO
            || $tipoDocumentoSunat == self::$NOTA_DEBITO) {
            //region ESTRUCTURA
            //A. DECLARACION DE VARIABLES PARA NOMBRE, REEMPLAZAR VALORES
            $_01_11_sRUCEmisor = $sRucEmisor;
            $_12_sGuion = '-';
            $_13_14_sTipoComprobante = $sCodigoTipoComprobante; //01|03|07|08
            $_15_sGuion = '-';
            $_16_19_sSerieComprobante = $sSerieComprobante; //FAAA ó BAAA
            $_20_sGuion = '-';
            $_21_28_sNroComprobante = $sNroComprobante; //LONGITUD VARIABLE DE 1 A 8 ; EJM: 00000001 Ó 1

            //$_29_sPunto = ".";
            //$_30_32_sExtension = "";//ZIP|XML
            //B. VALIDAR CONTENIDO DE LOS CAMPOS
            //TODO

            /* $_16_19_sSerieComprobante: Se espera que el primer carácter
            sea la constante “F” seguido por 3
            caracteres alfanuméricos para
            las Facturas y Notas asociadas
            ó B seguido de 3 caracteres
            para las Boletas de venta
            y Notas asociadas. */

            //C. CONSTRUIR NOMBRE DEL ARCHIVO
            //EJM: 20100066603-01-F001-1.ZIP
            $sNombreArchivo = $_01_11_sRUCEmisor
                . $_12_sGuion
                . $_13_14_sTipoComprobante
                . $_15_sGuion
                . $_16_19_sSerieComprobante
                . $_20_sGuion
                . $_21_28_sNroComprobante;
            //endregion
        } else if ($tipoDocumentoSunat === self::$COMUNICACION_BAJA) {
            //region ESTRUCTURA
            //A. DECLARACION DE VARIABLES PARA NOMBRE, REEMPLAZAR VALORES
            $_01_11_sRUCEmisor = $sRucEmisor;
            $_12_sGuion = '-';
            $_13_14_sTipoDeResumen = 'RA'; //RA
            $_15_sGuion = '-';
            $_16_23_sFechaGeneracionArchivo = $sFechaGeneracionArchivo; //YYYYMMDD
            $_24_sGuion = '-';

            /*if (Integer.parseInt(sNroCorrelativo) < 10) {
                sNroCorrelativo = "00" + sNroCorrelativo;
            } else if (Integer.parseInt(sNroCorrelativo) < 100) {
                sNroCorrelativo = "0" + sNroCorrelativo;
            }*/

            $_25_29_sNroCorrelativo = $sNroCorrelativo; //LONGITUD FIJA DE 3 EJM:001
            $_30_sPunto = '.';
            $_31_33_sExtension = ''; //ZIP|XML

            //B. VALIDAR CONTENIDO DE LOS CAMPOS
            //TODO
            //C. CONSTRUIR NOMBRE DEL ARCHIVO
            //EJM: 20100066603-RA-20110522-00001.ZIP
            $sNombreArchivo = $_01_11_sRUCEmisor
                . $_12_sGuion
                . $_13_14_sTipoDeResumen
                . $_15_sGuion
                . $_16_23_sFechaGeneracionArchivo
                . $_24_sGuion
                . $_25_29_sNroCorrelativo;
            //endregion
        } else if ($tipoDocumentoSunat === self::$RESUMEN_DIARIO) {
            //region ESTRUCTURA
            /*IMPORTANTE: A partir del 01 de enero de 2017, considerando la nueva estructura
            detallada del RESUMEN DIARIO (nuevo Anexo 5), el Resumen Diario deberá enviarse
            en bloques de 1,000 líneas. Cada bloque corresponderá a un número correlativo diferente.
            Los envíos son complementarios, es decir, no sustituyen al anteriormente enviado para el mismo día.*/
            //A. DECLARACION DE VARIABLES PARA NOMBRE, REEMPLAZAR VALORES
            $_01_11_sRUCEmisor = $sRucEmisor;
            $_12_sGuion = '-';
            $_13_14_sTipoDeResumen = 'RC'; //RC
            $_15_sGuion = '-';
            $_16_23_sFechaGeneracionArchivo = $sFechaGeneracionArchivo; //YYYYMMDD // Fecha que corresponde a la fecha de emision de la boletas y notas vinculadas
            $_24_sGuion = '-';
            $_25_29_sNroCorrelativo = $sNroCorrelativo; //LONGITUD VARIABLE DE 1 A 5

            //$_30_sPunto = ".";
            //$_31_33_sExtension = "";//ZIP|XML|EEEE
            //B. VALIDAR CONTENIDO DE LOS CAMPOS
            //TODO
            //C. CONSTRUIR NOMBRE DEL ARCHIVO
            //EJM: 20100066603-RC-20110522.ZIP

            $sNombreArchivo = $_01_11_sRUCEmisor
                . $_12_sGuion
                . $_13_14_sTipoDeResumen
                . $_15_sGuion
                . $_16_23_sFechaGeneracionArchivo
                . $_24_sGuion
                . $_25_29_sNroCorrelativo;
            //endregion
        } else if ($tipoDocumentoSunat === self::$COMPROBANTE_PERCEPCION || $tipoDocumentoSunat === self::$COMPROBANTE_RETENCION) {
            //region ESTRUCTURA
            //A. DECLARACION DE VARIABLES PARA NOMBRE, REEMPLAZAR VALORES
            $_01_11_sRUCEmisor = $sRucEmisor;
            $_12_sGuion = "-";
            $_13_14_sTipoComprobante = $sCodigoTipoComprobante; //40 (PERCEPCION)|20(RETENCION)
            $_15_sGuion = "-";
            $_16_19_sSerieComprobante = $sSerieComprobante; //PAAA ó RAAA
            $_20_sGuion = "-";
            $_21_28_sNroComprobante = $sNroComprobante; //LONGITUD VARIABLE DE 1 A 8 ; EJM: 00000001 Ó 1
            //$_29_sPunto = ".";
            //$_30_32_sExtension = "";//ZIP|XML
            //B. VALIDAR CONTENIDO DE LOS CAMPOS
            //TODO

            /*Serie del comprobante. Se espera que el primer
            carácter sea la constante “P” seguido por 3
            caracteres alfanuméricos para los comprobantes
            de percepción ó “R” seguido de 3 caracteres
            alfanuméricos para los comprobantes de retención.*/

            //C. CONSTRUIR NOMBRE DEL ARCHIVO
            //EJM: 20100066603-40-P001-1.ZIP

            $sNombreArchivo = $_01_11_sRUCEmisor
                . $_12_sGuion
                . $_13_14_sTipoComprobante
                . $_15_sGuion
                . $_16_19_sSerieComprobante
                . $_20_sGuion
                . $_21_28_sNroComprobante;
            //endregion
        } else if ($tipoDocumentoSunat === self::$RESUMEN_DIARIO_REVERSION) {
            //region ESTRUCTURA
            //Resumen diario de reversión de los comprobantes de percepción y retención:
            //A. DECLARACION DE VARIABLES PARA NOMBRE, REEMPLAZAR VALORES
            $_01_11_sRUCEmisor = $sRucEmisor;
            $_12_sGuion = '-';
            $_13_14_sTipoDeResumen = 'RR'; //RR
            $_15_sGuion = '-';
            $_16_23_sFechaGeneracionArchivo = $sFechaGeneracionArchivo; //YYYYMMDD
            $_24_sGuion = '-';
            $_25_29_sNroCorrelativo = $sNroCorrelativo; //LONGITUD VARIABLE DE 1 A 5
            //$_30_sPunto = ".";
            //$_31_33_sExtension = "";//ZIP|XML|EEE
            //B. VALIDAR CONTENIDO DE LOS CAMPOS
            //TODO
            //C. CONSTRUIR NOMBRE DEL ARCHIVO
            //EJM: 20100066603-RR-20150522-1.ZIP
            $sNombreArchivo = $_01_11_sRUCEmisor
                . $_12_sGuion
                . $_13_14_sTipoDeResumen
                . $_15_sGuion
                . $_16_23_sFechaGeneracionArchivo
                . $_24_sGuion
                . $_25_29_sNroCorrelativo;
            //endregion
        } else if ($tipoDocumentoSunat === self::$GUIA_REMISION) {
            //region ESTRUCTURA
            //A. DECLARACION DE VARIABLES PARA NOMBRE, REEMPLAZAR VALORES
            $_01_11_sRUCEmisor = sRucEmisor;
            $_12_sGuion = "-";
            $_13_14_sTipoComprobante = sCodigoTipoComprobante;//09 (REMISION TRANSPORTISTA)|31 (REMISION REMITENTE)
            $_15_sGuion = "-";
            $_16_19_sSerieComprobante = sSerieComprobante;// T###
            $_20_sGuion = "-";
            $_21_28_sNroComprobante = sNroComprobante;//LONGITUD VARIABLE DE 1 A 8 ; EJM: 00000001 Ó 1
            //$_29_sPunto = ".";
            //$_30_32_sExtension = "";//ZIP|XML
            //B. VALIDAR CONTENIDO DE LOS CAMPOS
            //TODO
            /*
            _16_19_sSerieComprobante: Serie de la guía de remisión electrónica.
            Se espera que el primer carácter sea la
            constante “T” seguido por tres caracteres
            alfanuméricos.*/

            //C. CONSTRUIR NOMBRE DEL ARCHIVO
            //EJM: 20100066603-09-T001-1.ZIP
            $sNombreArchivo = $_01_11_sRUCEmisor
                . $_12_sGuion
                . $_13_14_sTipoComprobante
                . $_15_sGuion
                . $_16_19_sSerieComprobante
                . $_20_sGuion
                . $_21_28_sNroComprobante;
            //endregion
        } else if ($tipoDocumentoSunat === self::$LOTE_FACTURAS_NOTAS) {
            //region ESTRUCTURA
            //Lotes de Facturas, notas de crédito y notas de débito electrónicas
            //A. DECLARACION DE VARIABLES PARA NOMBRE, REEMPLAZAR VALORES
            $_01_11_sRUCEmisor = $sRucEmisor;
            $_12_sGuion = '-';
            $_13_14_sTipoDeLotes = 'LT'; //LT (Lotes de Factura, notas de crédito y notas de débitos electrónicas.)
            $_15_sGuion = '-';
            $_16_23_sFechaGeneracionArchivo = $sFechaGeneracionArchivo; //YYYYMMDD
            $_24_sGuion = '-';
            $_25_29_sNroCorrelativo = $sNroCorrelativo; //LONGITUD VARIABLE DE 1 A 5
            //$_30_sPunto = ".";
            //$_31_33_sExtension = "";//ZIP|XML|EEE
            //B. VALIDAR CONTENIDO DE LOS CAMPOS
            //TODO
            //C. CONSTRUIR NOMBRE DEL ARCHIVO
            //EJM: 20100066603-LT-20160504-1.ZIP
            $sNombreArchivo = $_01_11_sRUCEmisor
                . $_12_sGuion
                . $_13_14_sTipoDeLotes
                . $_15_sGuion
                . $_16_23_sFechaGeneracionArchivo
                . $_24_sGuion
                . $_25_29_sNroCorrelativo;
            //endregion
        }

        return $sNombreArchivo;
    }

    public static function firmarComprobante($sNombreComprobanteXml, $sNombreCertificado, $sContrasenaCertificado) {
        $sRutaCertificadoPem = 'certificados/' . $sNombreCertificado . '.pem';
        if (!Storage::disk('public')->exists($sRutaCertificadoPem)) {
            self::crearCertificadoPem($sNombreCertificado, $sContrasenaCertificado);
        }

        $sRutaCompletaCertificadoPem = storage_path('app/public/' . $sRutaCertificadoPem);

        $signer = new SignedXml();
        $signer->setCertificateFromFile($sRutaCompletaCertificadoPem);

        $sRutaComprobanteXml = storage_path('app/public/comprobantes/' . $sNombreComprobanteXml);
        $xmlSigned = $signer->signFromFile($sRutaComprobanteXml);

        $sRutaCortaComprobanteFirmadoXml = 'comprobantes_firmados/' . $sNombreComprobanteXml;
        Storage::disk('public')->put($sRutaCortaComprobanteFirmadoXml, $xmlSigned);

        $xmlDoc = new \DOMDocument();
        $xmlDoc->loadXML($xmlSigned);
        $xmlNodesDs = $xmlDoc->getElementsByTagNameNS('http://www.w3.org/2000/09/xmldsig#', '*');
        $sDigestValue = '';
        foreach ($xmlNodesDs as $element) {
            if ($element->localName === 'DigestValue') {
                $sDigestValue = $element->nodeValue;
                break;
            }
        }

        return $sDigestValue;
    }

    private static function crearCertificadoPem($sNombreCertificado, $sContrasenaCertificado) {
        $sRutaCertificadoPfx = storage_path('app/public/certificados/' . $sNombreCertificado . '.pfx');
        $pfx = file_get_contents($sRutaCertificadoPfx);

        $certificate = new X509Certificate($pfx, $sContrasenaCertificado);
        $pem = $certificate->export(X509ContentType::PEM);

        $sRutaCertificadoPem = 'certificados/' . $sNombreCertificado . '.pem';
        Storage::disk('public')->put($sRutaCertificadoPem, $pem);
        //file_put_contents('certificate.pem', $pem);
    }

    public static function enviarSunat($iUrlProduccion, $sNombreComprobante, $sRucEmisor, $sUsuarioSol, $sClaveSol) {
        //$sUrlService = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
        $sUrlService = $iUrlProduccion == 1 ? self::$URL_SERVICE_PROD : self::$URL_SERVICE_TEST;
        $sUser = $sRucEmisor . $sUsuarioSol;

        $soapClient = new SoapClient();
        $soapClient->setService($sUrlService);
        $soapClient->setCredentials($sUser, $sClaveSol);

        $sender = new BillSender();
        $sender->setClient($soapClient);

        $sRutaComprobanteFirmadoXml = storage_path('app/public/comprobantes_firmados/' . $sNombreComprobante . '.xml');
        $xml = file_get_contents($sRutaComprobanteFirmadoXml);
        $result = $sender->send($sNombreComprobante, $xml);

        $respuesta = new Respuesta;
        if (!$result->isSuccess()) {
            // Error en la conexion con el servicio de SUNAT
            $respuesta->result = Result::ERROR;
            $respuesta->mensaje = $result->getError();
            return $respuesta;
        }

        $cdr = $result->getCdrResponse();
        $sRutaCortaCdr = 'cdrs/R-' . $sNombreComprobante . '.zip';
        Storage::disk('public')->put($sRutaCortaCdr, $result->getCdrZip());

        // Verificar CDR (Factura aceptada o rechazada)
        $sunat_status_code_cdr = (int)$cdr->getCode();
        $sunat_observaciones = '';
        if (count($cdr->getNotes()) > 0) {
            foreach ($cdr->getNotes() as $obs) {
                $sunat_observaciones .= 'OBS: ' . $obs . PHP_EOL;
            }
        }

        if ($sunat_status_code_cdr === 0) {
            $sunat_aceptado = 1;
        } else if ($sunat_status_code_cdr >= 2000 && $sunat_status_code_cdr <= 3999) {
            $sunat_aceptado = 0;
        } else {
            $sunat_aceptado = 0;
        }

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = $cdr->getDescription();
        $respuesta->data = [
            'sunat_aceptado' => $sunat_aceptado,
            'sunat_status_code_cdr' => $sunat_status_code_cdr,
            'sunat_observaciones' => $sunat_observaciones
        ];

        return $respuesta;
    }

    public static function generarPdfFacturaBoleta(SunatFacturaBoleta $sunatFacturaBoleta, $lstSunatFacturaBoletaDetalles, $sNombreComprobante) {
        //region CLIENTE
        $cliente = (new Client())
            ->setTipoDoc($sunatFacturaBoleta->tipo_documento_cliente)
            ->setNumDoc($sunatFacturaBoleta->nro_documento_cliente)
            ->setRznSocial($sunatFacturaBoleta->razon_social_cliente);
        //endregion

        //region DIRECCION EMPRESA EMISORA
        $direccion_emisor = (new Address())
            ->setUbigueo($sunatFacturaBoleta->domicilio_ubigeo)
            ->setDepartamento($sunatFacturaBoleta->domicilio_departamento)
            ->setProvincia($sunatFacturaBoleta->domicilio_provincia)
            ->setDistrito($sunatFacturaBoleta->domicilio_distrito)
            ->setUrbanizacion($sunatFacturaBoleta->domicilio_urbanizacion)
            ->setDireccion($sunatFacturaBoleta->domicilio_direccion_detallada)
            ->setCodLocal('0000');
        //endregion

        //region EMPRESA
        $empresa = (new Company())
            ->setRuc($sunatFacturaBoleta->empresa_numero_ruc)
            ->setRazonSocial($sunatFacturaBoleta->razon_social)
            ->setNombreComercial($sunatFacturaBoleta->nombre_comercial)
            ->setAddress($direccion_emisor);
        //endregion

        //region INVOICE
        $invoice = (new Invoice())
            ->setUblVersion($sunatFacturaBoleta->version_ubl)
            ->setTipoOperacion('0101') // Venta - Catalog. 51
            ->setTipoDoc($sunatFacturaBoleta->tipo_comprobante) // Factura - Catalog. 01
            ->setSerie($sunatFacturaBoleta->serie_comprobante)
            ->setCorrelativo($sunatFacturaBoleta->nro_comprobante)
            ->setFechaEmision(new \DateTime($sunatFacturaBoleta->fecha_emision . ' ' . $sunatFacturaBoleta->hora_emision . '-05:00')) // Zona horaria: Lima
            ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
            ->setTipoMoneda('PEN') // Sol - Catalog. 02
            ->setCompany($empresa)
            ->setClient($cliente)
            ->setMtoOperGravadas($sunatFacturaBoleta->total_valor_venta_gravada_monto)
            ->setMtoIGV($sunatFacturaBoleta->sumatoria_igv_monto_1)
            ->setTotalImpuestos($sunatFacturaBoleta->sumatoria_igv_monto_1)
            ->setValorVenta($sunatFacturaBoleta->total_valor_venta_neto)
            ->setSubTotal($sunatFacturaBoleta->importe_total_venta)
            ->setMtoImpVenta($sunatFacturaBoleta->importe_total_venta);
        //endregion

        //region ITEMS / DETALLES
        $lstItems = [];
        foreach ($lstSunatFacturaBoletaDetalles as $sunatFacturaBoletaDetalle) {
            $item = (new SaleDetail())
                ->setCodProducto('')
                ->setUnidad($sunatFacturaBoletaDetalle->unidad_medida) // Unidad - Catalog. 03
                ->setCantidad($sunatFacturaBoletaDetalle->cantidad)
                ->setMtoValorUnitario($sunatFacturaBoletaDetalle->valor_unitario)
                ->setDescripcion($sunatFacturaBoletaDetalle->descripcion)
                ->setMtoBaseIgv($sunatFacturaBoletaDetalle->valor_venta)
                ->setPorcentajeIgv(18.00) // 18%
                ->setIgv($sunatFacturaBoletaDetalle->igv_monto_1)
                ->setTipAfeIgv($sunatFacturaBoletaDetalle->igv_codigo_tipo) // Gravado Op. Onerosa - Catalog. 07
                ->setTotalImpuestos($sunatFacturaBoletaDetalle->igv_monto_1) // Suma de impuestos en el detalle
                ->setMtoValorVenta($sunatFacturaBoletaDetalle->valor_venta)
                ->setMtoPrecioUnitario($sunatFacturaBoletaDetalle->precio_venta_unitario_monto);

            array_push($lstItems, $item);
        }
        //endregion

        //region LEYENDA
        $legenda = (new Legend())
            ->setCode('1000') // Monto en letras - Catalog. 52
            ->setValue('');
        //endregion

        $invoice->setDetails($lstItems)->setLegends([$legenda]);

        //region HTML REPORT
        $htmlReport = new HtmlReport();
        $htmlReport->setTemplate('invoice.html.twig');
        //endregion

        $pdfReport = new PdfReport($htmlReport);
        $pdfReport->setOptions([
            'no-outline',
            'viewport-size' => '1280x1024',
            'page-width' => '21cm',
            'page-height' => '29.7cm',
        ]);

        $sRutaEjecutable = storage_path('app/public/comprobantes/wkhtmltopdf'); //LINUX
        //$sRutaEjecutable = storage_path('app/public/comprobantes/wkhtmltopdf.exe');
        $pdfReport->setBinPath($sRutaEjecutable); // Ruta relativa o absoluta de wkhtmltopdf

        $sRutaLogoEcovalle = public_path('img/logo_ecovalle.png');
        $parametros = [
            'system' => [
                'logo' => file_get_contents($sRutaLogoEcovalle), //Logo de Empresa
                'hash' => $sunatFacturaBoleta->sunat_digest_value, //Valor Resumen
            ],
            'user' => [
                /*'extras' => [
                    // Leyendas adicionales
                    ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'],
                    ['name' => 'VENDEDOR', 'value' => 'GITHUB SELLER'],
                ],*/
                'footer' => '<p>AGROENSANCHA S.R.L.</p>'
            ]
        ];

        $pdf = $pdfReport->render($invoice, $parametros);

        $respuesta = new Respuesta;
        if ($pdf === null) {
            $respuesta->result = Result::ERROR;
            $respuesta->mensaje = 'No se pudo generar el PDF del comprobante';
            $respuesta->data = $pdfReport->getExporter()->getError();
            return $respuesta;
        }

        $sRutaCortaPdf = 'comprobantes/' . $sNombreComprobante . '.pdf';
        Storage::disk('public')->put($sRutaCortaPdf, $pdf);

        $respuesta->result = Result::SUCCESS;
        return $respuesta;
    }
}
