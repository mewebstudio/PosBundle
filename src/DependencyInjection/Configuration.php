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
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('banks')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                    ->performNoDeepMerging()
                    ->children()
                        ->scalarNode('gateway_class')->isRequired()->end()
                        ->scalarNode('lang')->defaultValue(PosInterface::LANG_TR)->end()
                        ->arrayNode('credentials')->isRequired()
                            ->children()
                                ->scalarNode('payment_model')->isRequired()->end()

                                ->scalarNode('merchant_id')
                                    ->info("EstPos|ToslaPos: ClientId;\nPosNet|GarantiPos|PayForPos|KuveytPos|VakifKatilimPos|PayFlexV4Pos|PayFlexCPV4Pos: MerchantId;\nInterPos: ShopCode;\nAkbankPos: MerchantSafeId;")
                                ->end()
                                ->scalarNode('sub_merchant_id')->end()
                                ->scalarNode('user_name')
                                    ->info("EstPos: KullanıcıAdı;\nPosNet: PosNetId;\nPayForPos|InterPos: UserCode;\nGarantiPos: ProvUserID;\nKuveytPos|VakifKatilimPos: UserName;\nToslaPos: ApiUser")
                                ->end()
                                ->scalarNode('terminal_id')
                                    ->info("PosNet|GarantiPos|PayFlexV4Pos|PayFlexCPV4Pos: TerminalId\nKuveytPos|VakifKatilimPos: CustomerId;\nAkbankPos: TerminalSafeId")
                                ->end()
                                ->scalarNode('user_password')
                                    ->info("PayFlexV4Pos|PayFlexCPV4Pos: Password;\nEstPos: KullaniciSifresi;\nPayForPos|InterPos: UserPass;\nGarantiPos: ProvisionPassword;")
                                ->end()
                                ->scalarNode('enc_key')
                                    ->info("EstPos|GarantiPos: StoreKey;\nPosNet: EncKey;\nPayForPos|InterPos: MerchantPass;\nKuveytPos: Password;")
                                ->end()
                                ->scalarNode('refund_user_name')->info("GarantiPos: ProvUserID;")->end()
                                ->scalarNode('refund_user_password')->info("GarantiPos: ProvisionPassword")->end()
                            ->end()
                        ->end()
                        ->arrayNode('gateway_endpoints')->isRequired()
                            ->children()
                                ->scalarNode('payment_api')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('gateway_3d')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('gateway_3d_host')
                                    ->cannotBeEmpty()
                                    ->info('required for 3D host payments')
                                ->end()
                                ->scalarNode('query_api')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        ->booleanNode('test_mode')->defaultFalse()->end()
                    ->end()
                    ->end()
                    ->defaultValue([])
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
