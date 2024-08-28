import {
  FlatList,
  StyleSheet,
  Text,
  View,
  Image,
  Pressable,
} from "react-native";
import React from "react";
import { colors } from "../Constants";
import { FontAwesome } from "@expo/vector-icons";
import { useNavigation } from "@react-navigation/native";

const FoodsList = ({ foodList }) => {
  const navigation = useNavigation();
  return (
    <View>
      <FlatList
        data={foodList}
        renderItem={({ item }) => (
          <Pressable
            // We are navigating to the next screen and we are passing the data to
            // that screen that we are navigating to
            onPress={() => navigation.navigate("FoodDetail", { item: item })}
            style={{
              backgroundColor: colors.COLOR_LIGHT,
              borderRadius: 16,
              marginVertical: 16,
              alignItems: "center",
              paddingHorizontal: 8,
              paddingVertical: 26,

              shadowColor: "#000",
              shadowOffset: { width: 0, height: 4 },
              shadowRadius: 7,
              shadowOpacity: 0.1,
            }}
            key={item.id}
          >
            <Image
              key={item.id}
              source={item.image}
              style={{
                width: 150,
                height: 150,
                resizeMode: "center",
                borderRadius: 75,
                marginBottom: 10,
              }}
            />
            <Text style={{ fontSize: 22, fontWeight: "500" }}>{item.name}</Text>
            <View style={{ flexDirection: "row", marginTop: 5 }}>
              <Text style={{ fontSize: 16 }}>{item.time}</Text>
              <Text> | </Text>
              <View style={{ flexDirection: "row" }}>
                <Text key={item.id} style={{ marginRight: 4, fontSize: 16 }}>
                  {item.calories}
                </Text>
                <FontAwesome
                  name="star"
                  size={16}
                  color={colors.COLOR_PRIMARY}
                />
              </View>
            </View>
          </Pressable>
        )}
        numColumns={2}
        columnWrapperStyle={{
          justifyContent: "space-between",
        }}
        showsVerticalScrollIndicator={false}
      />
    </View>
  );
};

export default FoodsList;

const styles = StyleSheet.create({});
