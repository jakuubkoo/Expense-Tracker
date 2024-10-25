import {Alert, FlatList, Text, View} from 'react-native'
import React, {useEffect, useState} from 'react'
import { SafeAreaView } from 'react-native-safe-area-context'

import images from '../../constants/images'
import axios from "axios";
import AsyncStorage from "@react-native-async-storage/async-storage";
import {router} from "expo-router";
import api from "../../components/API/api";
import * as SecureStore from "expo-secure-store";
import {useGlobalContext} from "../../context/GlobalProvider";

const Home = () => {

    const updated = new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
    }).format(new Date())

    const { user } = useGlobalContext();

    const data = [
        { key: 'Trades', value: '0' },
        { key: 'P/L', value: '0$' },
        { key: 'Winrate', value: '0%' },
        { key: 'Gain', value: '0%' },
    ];

    return (
        <SafeAreaView className="bg-primary h-full">
            <View className='flex flex-col w-full'>
                <View className='flex flex-col w-full mt-2'>
                    <Text
                        className='text-content-primary text-[20px] font-[700] leading-loose font-amedium px-4 ml-5 mt-3'>
                        Welcome Back
                    </Text>

                    <Text
                        className='font-aregular text-content-primary text-[35px] font-400 leading-snug tracking-wide px-4 ml-5'>
                        {user.firstName}
                    </Text>
                </View>

                <View className='bg-white rounded-[16px] w-[calc(90vw)] mt-6 shadow border border-neutral-200 p-6 mx-auto'>
                    <Text className='text-neutral-950 text-[22px] font-bold leading-7'>
                        Daily Overview
                    </Text>
                    <Text className='text-content-tertiary text-[12px] font-normal leading-snug tracking-tight font-SatoshiRegular mt-0.5'>
                        Updated {updated}
                    </Text>

                    <View className='mt-4'>
                        <FlatList
                            data={data}
                            renderItem={({ item }) => (
                                <View className="flex-1 m-2">
                                    <Text className="text-content-primary text-[18px] font-normal leading-snug tracking-tight font-aregular mt-1">
                                        {item.key}: <Text className="text-content-tertiary">{item.value}</Text>
                                    </Text>
                                </View>
                            )}
                            keyExtractor={(item) => item.key}
                            numColumns={2}
                            columnWrapperStyle={{ justifyContent: 'space-between' }}
                        />
                    </View>
                </View>
            </View>
        </SafeAreaView>
    )
}

export default Home