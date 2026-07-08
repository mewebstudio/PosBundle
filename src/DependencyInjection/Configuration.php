<?php

namespace Mews\PosBundle\DependencyInjection;

use Mews\Pos\PosInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('mews_pos');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('banks')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                    ->performNoDeepMerging()
                    ->children()
                        ->scalarNode('gateway_class')->isRequired()->end()
                        ->arrayNode('credentials')->isRequired()
                            ->children()
                                ->scalarNode('merchant_id')
                                    ->info("AssecoPos|ToslaPos: ClientId;\nPosNetPos|GarantiPos|PayForPos|KuveytPos|VakifKatilimPos|PayFlexV4Pos|PayFlexCPV4Pos: MerchantId;\nInterPos: ShopCode;\nAkbankPos: MerchantSafeId;\nIyzicoPos: ApiKey;\nPayTrPos: MerchantId;")
                                ->end()
                                ->scalarNode('sub_merchant_id')->end()
                                ->scalarNode('user_name')
                                    ->info("AssecoPos: KullanıcıAdı;\nPosNetPos: PosNetId;\nPayForPos|InterPos: UserCode;\nGarantiPos: ProvUserID;\nKuveytPos|VakifKatilimPos: UserName;\nToslaPos: ApiUser;\nParamPos|Param3DHostPos: CLIENT_USERNAME;")
                                ->end()
                                ->scalarNode('terminal_id')
                                    ->info("PosNetPos|GarantiPos|PayFlexV4Pos|PayFlexCPV4Pos: TerminalId;\nKuveytPos|VakifKatilimPos: CustomerId;\nAkbankPos: TerminalSafeId;\nParamPos|Param3DHostPos: Terminal_ID (optional);")
                                ->end()
                                ->scalarNode('user_password')
                                    ->info("PayFlexV4Pos|PayFlexCPV4Pos: Password;\nAssecoPos: KullaniciSifresi;\nPayForPos|InterPos: UserPass;\nGarantiPos: ProvisionPassword;\nPayTrPos: MerchantSalt;\nParamPos|Param3DHostPos: CLIENT_PASSWORD;")
                                ->end()
                                ->scalarNode('secret_key')
                                    ->info("AssecoPos|GarantiPos: StoreKey;\nPosNetPos: EncKey;\nPayForPos|InterPos: MerchantPass;\nKuveytPos|VakifKatilimPos: StoreKey;\nAkbankPos: SecretKey;\nToslaPos: ApiPass;\nParamPos|Param3DHostPos: GUID;\nIyzicoPos: SecretKey;\nPayTrPos: MerchantKey;")
                                ->end()
                                ->scalarNode('refund_user_name')->info('GarantiPos: ProvUserID;')->end()
                                ->scalarNode('refund_user_password')->info('GarantiPos: ProvisionPassword')->end()
                                ->scalarNode('mbr_id')->info('PayForPos: MbrId')->end()
                            ->end()
                        ->end()
                        ->arrayNode('gateway_endpoints')->isRequired()
                            ->children()
                                ->scalarNode('payment_api')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('gateway_3d')->cannotBeEmpty()->end()
                                ->scalarNode('gateway_3d_host')
                                    ->cannotBeEmpty()
                                    ->info('required for 3D host payments')
                                ->end()
                                ->scalarNode('query_api')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        ->arrayNode('gateway_configs')
                            ->children()
                                ->booleanNode('test_mode')->defaultFalse()->end()
                                ->booleanNode('disable_3d_hash_check')
                                    ->defaultFalse()
                                    ->info('Hash kontrolü kütühaneden dolayı başarısız sonuçlanıyorsa bu ayarla devre dışı bırakılabilir.
Ancak hash kontrolünün devre dışı bırakılması güvenlik açığı oluşturabilir.')
                                ->end()
                                ->scalarNode('lang')
                                    ->defaultValue(PosInterface::LANG_TR)
                                    ->info('Default language for request data. Used by gateways that support multilingual responses.')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->end()
                    ->defaultValue([])
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
